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
            <h1>Отчеты</h1>
            <el-button
                type="primary"
                @click="add()"
            >
                Добавить отчет
            </el-button>
        </el-row>

        <el-row
            type="flex"
            align="middle"
        >
            <el-tabs
                v-model="filter_data.active_tab"
                class="stylized-tabs"
            >
                <el-tab-pane
                    label="Флюрография"
                    name="fluorography"
                    style="margin: 0;"
                />
                <el-tab-pane
                    label="Медкомиссия"
                    name="medical_board"
                    style="margin: 0;"
                />
            </el-tabs>

            <div class="header-divider"/>

            <el-date-picker
                class="date-range"
                v-model="filter_data.date_range"
                format="MMMM yyyy"
                value-format="yyyy-MM-dd"
                :type="filter_data.group_by === 'month' ? 'monthrange' : 'daterange'"
                align="right"
                start-placeholder="Начальная дата"
                end-placeholder="Конечная дата"
            />

        </el-row>

        <div
            class="entities-list"
            v-loading="loading"
        >

            <el-card shadow="never" class="entities-list__head">
                <el-row>
                    <el-col :span="10">
                        ФИО
                    </el-col>
                    <el-col :span="4">
                        Дата
                    </el-col>
                    <el-col :span="4">
                        Статус
                    </el-col>
                    <el-col :span="2">
                        Кол.фото
                    </el-col>
                    <el-col :span="4">
                        Действия
                    </el-col>
                </el-row>
            </el-card>

            <div v-if="reports_grouped_by_month && reports_grouped_by_month.length">
                <div
                    class="entities-list__group"
                    v-for="group in reports_grouped_by_month"
                    :key="group.created_at"
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
                                    <el-col :span="4">
                                        {{ report.created_at | formatDate }}
                                    </el-col>
                                    <el-col :span="4">
                                        <div
                                            :class="{
                                            'color-success': report.is_done,
                                            'color-info': !report.is_done,
                                        }"
                                        >
                                            {{ report.is_done ? 'Выполнено' : 'Не выполнено' }}
                                        </div>
                                    </el-col>
                                    <el-col :span="2">
                                        <template v-if="report.attachments">
                                            {{ report.attachments.length }}
                                        </template>
                                    </el-col>
                                    <el-col :span="4">
                                        <span
                                            class="color-primary clickable"
                                            style="margin-right: 10px;"
                                            @click="edit(report)"
                                        >
                                            Изменить
                                        </span>
                                        <span
                                            class="color-danger clickable"
                                            style="margin-right: 10px;"
                                            @click="doDelete(report)"
                                        >
                                            Удалить
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
                Ничего не найдено
            </div>
        </div>

    </div>
</template>

<script>
    import moment from 'moment';
    import {formatDate} from "../../util/filters";
    import {mapGetters, mapActions} from 'vuex';

    export default {
        name: "Reports",

        filters: {
            formatDate,
        },

        data() {
            return {
                filter_data: {
                    active_tab: 'fluorography',
                    group_by: 'month',
                    date_range: [],
                },
                loading: false,
            }
        },

        created() {
            this.fetchData();
        },

        computed: {
            ...mapGetters(['reports_grouped_by_month']),
        },

        methods: {
            ...mapActions(['getReports', "deleteReport"]),

            async fetchData() {
                this.loading = true;

                await this.getReports()
                    .catch((err) => {
                        this.$message.error(errorHandler(err).message || 'Не удалось загрузить комиссии')
                    });

                this.loading = false;
            },

            formatGroupTitle(group) {
                if (this.filter_data.group_by === 'month') {
                    return moment(group.created_at).format('MMMM');
                } else {
                    return moment(group.created_at).format('dd, DD.MM.YYYY');
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
                        'Вы действительно хотите удалить "' + item.full_name + '"?',
                        'Подтвердите действие',
                        {
                            confirmButtonText: 'Удалить',
                            cancelButtonText: 'Отмена',
                        }
                    )
                    .catch(_.noop);

                if (!confirm) return;

                this.loading = true;

                await this
                    .deleteReport({id: item.id})
                    .catch(err => {
                        this.$message.error(errorHandler(err).message || 'Не удалось удалить пользователя');
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