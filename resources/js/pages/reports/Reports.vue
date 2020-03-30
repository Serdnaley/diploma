<template>
    <div
        class="history"
        v-loading="loading"
    >
        <h1 class="title">История</h1>

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
                :type="filter_data.group_by === 'month' ? 'monthrange' : 'daterange'"
                align="right"
                start-placeholder="Начальная дата"
                end-placeholder="Конечная дата"
            />

        </el-row>

        <div class="entities-list">

            <el-card shadow="never" class="entities-list__head">
                <el-row>
                    <el-col :span="12">
                        ФИО
                    </el-col>
                    <el-col :span="4">
                        Дата
                    </el-col>
                    <el-col :span="4">
                        Статус
                    </el-col>
                    <el-col :span="4">
                        Кол.фото
                    </el-col>
                </el-row>
            </el-card>

            <div
                class="entities-list__group"
                v-for="group in filter_data.group_by === 'month' ? reports_grouped_by_month : reports_grouped_by_date"
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
                                <el-col :span="12">
                                    {{ report.user.full_name }}
                                </el-col>
                                <el-col :span="4">
                                    {{ report.created_at }}
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
                                <el-col :span="4">
                                    {{ report.attachments.length }} фото
                                </el-col>
                            </el-row>
                        </el-card>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    import moment from 'moment';
    import {mapGetters} from 'vuex';

    export default {
        name: "History",

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

        computed: {
            ...mapGetters(['reports_grouped_by_month', "reports_grouped_by_date"]),
        },

        methods: {
            formatGroupTitle(group) {
                if (this.filter_data.group_by === 'month') {
                    return moment(group.created_at).format('MMMM');
                } else {
                    return moment(group.created_at).format('dd, DD.MM.YYYY');
                }
            }
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