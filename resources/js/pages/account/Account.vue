<template>
    <div
        class="account"
        v-loading="loading"
    >

        <router-view/>

        <div class="account__head">
            <h1>Добро пожаловать, {{ $auth.user().first_name }}</h1>
            <el-button
                type="primary"
                @click="add()"
            >
                Добавить отчет
            </el-button>
        </div>

        <div
            class="entities-list"
        >

            <div v-if="user_reports && user_reports.length">
                <div
                    class="entities-list__group-item"
                    v-for="report in user_reports"
                    :key="report.id"
                >
                    <el-card shadow="never">
                        <el-row
                            type="flex"
                            justify="space-between"
                            align="middle"
                        >
                            <div class="">
                                Выполнено {{ report.date | formatDate }}
                            </div>
                            <el-button
                                type="primary"
                                plain
                                size="mini"
                                @click="edit(report)"
                            >
                                Изменить
                            </el-button>
                        </el-row>
                    </el-card>
                </div>
            </div>

            <div
                class="entities-list__no-items"
                v-else
            >
                Ваша история пуста
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

        filters: {
            formatDate,
        },

        data() {
            return {
                loading: false,
            }
        },

        beforeRouteEnter(to, from, next) {
            next(vm => vm.fetchData());
        },

        computed: {
            ...mapGetters(['user_reports']),
        },

        methods: {
            ...mapActions(['getUserReports']),

            async fetchData() {
                this.loading = true;

                await this
                    .getUserReports(this.$auth.user())
                    .catch((err) => {
                        this.$message.error(errorHandler(err).message || 'Не удалось загрузить отчеты')
                    });

                this.loading = false;
            },

            add() {
                this.$router.push({
                    name: 'AccountAddReport',
                });
            },

            edit(report) {
                this.$router.push({
                    name: 'AccountEditReport',
                    params: {report_id: report.id}
                });
            },
        },
    }
</script>

<style lang="scss">
    @import '../../../scss/variables';

    .account {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;

        &__head {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 75vh;

            .el-button {
                margin-top: 50px;
                margin-bottom: 50px;
            }
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

        .entities-list {
            &__group {
                &-item {
                    width: 400px;
                    max-width: 100%;
                }
            }
        }
    }
</style>