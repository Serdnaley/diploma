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
                class="tabs"
            >
                <el-tab-pane
                    label="Флюрография"
                    name="fluorography"
                />
                <el-tab-pane
                    label="Медкомиссия"
                    name="medical_board"
                />
            </el-tabs>

            <div class="header-divider"/>

            <el-tabs
                v-model="filter_data.group_by"
                class="tabs"
            >
                <el-tab-pane
                    label="По месяцам"
                    name="month"
                />
                <el-tab-pane
                    label="По дням"
                    name="day"
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

        <el-card shadow="never" class="list__head">
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

        <div class="list">
            <div
                class="list__group"
                v-for="group in filter_data.group_by === 'month' ? reports_grouped_by_month : reports_grouped_by_date"
                :key="group.created_at"
            >
                <div class="list__group-title">
                    {{ formatGroupTitle(group) }}
                </div>
                <div class="list__group-items">
                    <div
                        class="list__group-item"
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

        mounted() {
            axios({
                url: 'some',
                method: 'get'
            });
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
    @import '../../scss/variables';

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

        .tabs {
            display: flex;
            text-transform: uppercase;

            .el-tabs {
                &__header {
                    margin: 0;
                }

                &__nav-wrap {
                    margin: 0;

                    &::after {
                        display: none;
                    }
                }

                &__item {
                    position: relative;
                    padding: 5px 30px 5px 0;
                    height: auto;
                    line-height: 2em;

                    &::after {
                        content: '';
                        display: block;
                    }
                }

                &__active-bar {
                    transition: width 0.3s cubic-bezier(0.645, 0.045, 0.355, 1),
                    transform 0.3s cubic-bezier(0.645, 0.045, 0.355, 1);
                }
            }
        }

        .date-range.el-input__inner:not(:hover):not(:focus):not(.is-active) {
            border-color: transparent;
        }

        .list {

            &__head {
                background: $--color-info-light;
                margin-top: 30px;

                .el-card {
                    &__body {
                        padding: 10px 20px;
                    }
                }
            }

            &__group {
                position: relative;

                &-title {
                    position: sticky;
                    top: 0;
                    padding: 25px 0 5px 0;
                    border-bottom: 2px solid $--color-text-regular;
                    color: $--color-text-regular;
                    background: $--color-white;
                    font-size: $--font-size-medium;
                    font-weight: 600;
                    z-index: 2;
                    text-transform: uppercase;
                }

                &-items {
                    margin-top: 15px;
                }

                &-item {
                    margin-bottom: 15px;
                }
            }
        }
    }
</style>