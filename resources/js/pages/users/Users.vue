<template>
    <div
        class="users"
        v-loading="loading"
    >
        <h1 class="title">Пользователи</h1>

        <div class="entities-list">

            <el-card shadow="never" class="entities-list__head">
                <el-row>
                    <el-col :span="12">
                        ФИО
                    </el-col>
                    <el-col :span="4">
                        Роль
                    </el-col>
                    <el-col :span="4">
                        Статус
                    </el-col>
                    <el-col :span="4">
                        Кол.фото
                    </el-col>
                </el-row>
            </el-card>

            <div class="entities-list__group-items">
                <div
                    class="entities-list__group-item"
                    v-for="user in users"
                    :key="user.id"
                >
                    <el-card shadow="never">
                        <el-row>
                            <el-col :span="12">
                                {{ user.full_name }}
                            </el-col>
                            <el-col :span="4">
                                {{ user.created_at | formatDate }}
                            </el-col>
                            <el-col :span="4">
                                <div>
                                    {{ user.is_done ? 'Выполнено' : 'Не выполнено' }}
                                </div>
                            </el-col>
                            <el-col :span="4">
                                {{ user.length }} фото
                            </el-col>
                        </el-row>
                    </el-card>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
    import {formatDate} from '../../util/filters';
    import {mapGetters, mapActions} from 'vuex';
    import {errorHandler} from "../../util";

    export default {
        name: "Users",

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

        beforeRouteEnter(to, from, next) {
            next(vm => vm.fetchData());
        },

        computed: {
            ...mapGetters(['users']),
        },

        methods: {
            ...mapActions(['getUsers']),

            async fetchData() {
                this.loading = true;

                await this.getUsers()
                    .catch((err) => {
                        this.$message.error(errorHandler(err).message || 'Не удалось загрузить пользователей')
                    });

                this.loading = false;
            },
        },
    }
</script>

<style lang="scss">
    @import '../../../scss/variables';

    .users {

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