<template>
    <div class="history">
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

            <div>
                <span
                    class="clickable"
                    :class="{ 'color-primary': filter_data.group_by === 'day' }"
                    @click="filter_data.group_by = 'day'"
                >
                    По дням
                </span>
                <span>/</span>
                <span
                    class="clickable"
                    :class="{ 'color-primary': filter_data.group_by === 'month' }"
                    @click="filter_data.group_by = 'month'"
                >
                    по месяцам
                </span>
            </div>

            <div class="header-divider"/>

            <el-date-picker
                v-model="filter_data.date_range"
                type="daterange"
                size="mini"
                start-placeholder="С"
                end-placeholder="ПО"
            />

        </el-row>

        <div class="list">

        </div>

    </div>
</template>

<script>
    export default {
        name: "History",

        data() {
            return {
                filter_data: {
                    active_tab: 'fluorography',
                    group_by: 'month',
                    date_range: [],
                },
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
                    &::after {
                        display: none;
                    }
                }
            }

            &__item {
                position: relative;
                margin-right: 30px;
                padding: 5px 0;

                &::after {
                    content: '';
                    display: block;
                }
            }
        }
    }
</style>