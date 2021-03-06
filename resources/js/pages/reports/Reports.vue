<template>
    <div
        class="history"
    >

        <router-view/>

        <el-row
            type="flex"
            justify="space-between"
            align="middle"
            class="title"
        >
            <h1>Звіти</h1>
            <el-button
                type="primary"
                class="screen-only"
                @click="add()"
            >
                Додати звіт
            </el-button>
        </el-row>

        <el-row
            type="flex"
            align="middle"
            class="screen-only"
        >
            <el-tabs
                v-model="filter_data.type"
                class="stylized-tabs"
            >
                <el-tab-pane
                    label="Флюорографія"
                    name="fluorography"
                    style="margin: 0;"
                />
                <el-tab-pane
                    label="Медкомісія"
                    name="medical_board"
                    style="margin: 0;"
                />
            </el-tabs>

            <div class="header-divider"/>

            <el-date-picker
                class="date-range"
                v-model="filter_data.date"
                format="MMMM yyyy"
                value-format="yyyy-MM-dd"
                type="monthrange"
                align="right"
                start-placeholder="Початкова дата"
                end-placeholder="Кінцева дата"
                :clearable="false"
            />

            <div class="header-divider"/>

            <remote-select
                v-model="filter_data.category"
                :method="getCategories"
                clearable
                placeholder="Вибрати комісію"
            >
                <el-option
                    value="all"
                    label="Усі"
                />
                <el-option
                    value="without"
                    label="Без комісії"
                />
                <template v-slot:item="{item}">
                    <el-option
                        :value="item.id"
                        :label="item.name"
                    />
                </template>
            </remote-select>

        </el-row>

        <div
            class="entities-list"
            v-loading="loading"
        >

            <el-card shadow="never" class="entities-list__head">
                <el-row>
                    <el-col :span="10">
                        ПІБ
                    </el-col>
                    <el-col :span="8">
                        Статус
                    </el-col>
                    <el-col :span="2">
                        Фото
                    </el-col>
                    <el-col
                        :span="4"
                        class="screen-only"
                    >
                        Дії
                    </el-col>
                </el-row>
            </el-card>

            <div v-if="reports_grouped_by_month && reports_grouped_by_month.length">
                <div
                    class="entities-list__group"
                    v-for="group in reports_grouped_by_month"
                    :key="group.date"
                >
                    <div class="entities-list__group-title">
                        {{ formatGroupTitle(group) }}
                    </div>
                    <div class="entities-list__group-items">
                        <div
                            class="entities-list__group-item"
                            v-for="report in group.items"
                            :key="report.id"
                        >
                            <el-card shadow="never">
                                <el-row>
                                    <el-col :span="10">
                                        <template v-if="report.user">
                                            {{ report.user.full_name }}
                                        </template>
                                    </el-col>
                                    <el-col :span="8">
                                        <div
                                            v-if="report.status === 'new'"
                                            class="color-danger"
                                        >
                                            Немає звітів
                                        </div>
                                        <div
                                            v-if="report.status === 'planned'"
                                            class="color-info"
                                        >
                                            Заплановано на {{ report.date | formatDate }}
                                        </div>
                                        <div
                                            v-if="report.status === 'expired'"
                                            class="color-info"
                                        >
                                            <template v-if="formatDate(report.date) !== formatDate(report.term)">
                                                Заплановано на {{ report.date | formatDate }},
                                            </template>
                                            <div class="color-danger">
                                                Треба було пройти {{ report.term | formatDate }}
                                            </div>
                                        </div>
                                        <div
                                            v-if="report.status === 'done'"
                                            class="color-success"
                                        >
                                            Виконано {{ report.date | formatDate }}
                                        </div>
                                    </el-col>
                                    <el-col :span="2">
                                        <template v-if="report.attachments">
                                            {{ report.attachments.length }}
                                        </template>
                                    </el-col>
                                    <el-col
                                        v-if="report.id"
                                        :span="4"
                                        class="screen-only"
                                    >
                                        <span
                                            class="color-primary clickable"
                                            style="margin-right: 10px;"
                                            @click="edit(report)"
                                        >
                                            Змінити
                                        </span>
                                        <span
                                            class="color-danger clickable"
                                            style="margin-right: 10px;"
                                            @click="doDelete(report)"
                                        >
                                            Видалити
                                        </span>
                                    </el-col>
                                </el-row>
                            </el-card>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="entities-list__no-items"
                v-else
            >
                Нічого не знайдено
            </div>
        </div>

    </div>
</template>

<script>
    import moment from 'moment';
    import {formatDate} from "../../util/filters";
    import {errorHandler} from "../../util";
    import {mapGetters, mapActions} from 'vuex';
    import RemoteSelect from "../../components/RemoteSelect";

    export default {
        name: "Reports",

        components: {
            RemoteSelect,
        },

        filters: {
            formatDate,
        },

        data() {
            return {
                filter_data_defaults: {
                    type: 'fluorography',
                    date: [
                        moment().subtract(1, 'year').format('YYYY-MM-DD'),
                        moment().format('YYYY-MM-DD'),
                    ],
                    category: 'all',
                },
                filter_data: {},

                loading: false,
            }
        },

        beforeRouteEnter(to, from, next) {
            next(vm => vm.fetchData());
        },

        computed: {
            ...mapGetters(['reports_grouped_by_month']),
        },

        watch: {

            '$route.query': {
                handler(query) {
                    this.filter_data = _.defaults({}, query, this.filter_data_defaults);
                    this.fetchData();
                },
                immediate: true,
                deep: true,
            },

            filter_data: {
                handler() {
                    const query = _.transform(this.filter_data, (result, val, key) => {
                        if (this.filter_data_defaults[key] !== val && val) {
                            result[key] = val;
                        }
                    });

                    this.$router.replace({query}).catch(_.noop);
                },
                deep: true
            },
        },

        methods: {
            ...mapActions(['getReports', "deleteReport", "getCategories"]),

            formatDate,

            async fetchData() {
                this.loading = true;

                await this
                    .getReports({
                        from: this.filter_data.date[0],
                        to: this.filter_data.date[1],
                        type: this.filter_data.type,
                        category: this.filter_data.category,
                    })
                    .catch((err) => {
                        this.$message.error(errorHandler(err).message || 'Не вдалося завантажити звіт')
                    });

                this.loading = false;
            },

            formatGroupTitle(group) {
                if (group.date) {
                    return moment(group.date).format('MMMM, YYYY');
                } else {
                    return 'Без жодного звіту';
                }
            },

            add() {
                this.$router.push({
                    name: 'AddReport',
                });
            },

            edit(report) {
                this.$router.push({
                    name: 'EditReport',
                    params: {report_id: report.id}
                });
            },

            async doDelete(item) {

                let confirm = await this
                    .$confirm(
                        'Ви дійсно хочете видалити "' + item.full_name + '"?',
                        'Підтвердіть дію',
                        {
                            confirmButtonText: 'Видалити',
                            cancelButtonText: 'Відміна',
                        }
                    )
                    .catch(_.noop);

                if (!confirm) return;

                this.loading = true;

                await this
                    .deleteReport({id: item.id})
                    .catch(err => {
                        this.$message.error(errorHandler(err).message || 'Не вдалось видалити звіт');
                    });

                this.loading = false;
            },
        },
    }
</script>

<style lang="scss">
    @import '../../../scss/variables';

    .history {

        .title {
            margin-bottom: 30px;
        }

        .header-divider {
            height: 30px;
            width: 1px;
            background: $--color-text-placeholder;
            margin: 0 30px;
        }

        .date-range.el-input__inner:not(:hover):not(:focus):not(.is-active) {
            border-color: transparent;
        }

    }
</style>
