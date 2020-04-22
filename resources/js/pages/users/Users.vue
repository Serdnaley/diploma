<template>
    <div class="users">

        <router-view/>

        <el-row
            type="flex"
            justify="space-between"
            align="middle"
            class="title"
        >
            <h1>Пользователи</h1>
            <el-button
                type="primary"
                class="screen-only"
                @click="add()"
            >
                Добавить пользователя
            </el-button>
        </el-row>

        <div
            class="entities-list"
            v-loading="loading"
        >

            <el-card shadow="never" class="entities-list__head">
                <el-row>
                    <el-col :span="16">
                        ФИО
                    </el-col>
                    <el-col :span="3">
                        Роль
                    </el-col>
                    <el-col :span="4">
                        Действия
                    </el-col>
                </el-row>
            </el-card>

            <div
                class="entities-list__group-items"
                v-if="users && users.length"
            >
                <div
                    class="entities-list__group-item"
                    v-for="user in users"
                    :key="user.id"
                >
                    <el-card shadow="never">
                        <el-row>
                            <el-col :span="16">
                                {{ user.full_name }}
                                <span
                                    v-if="!user.telegram_chat_id"
                                    class="color-danger"
                                >
                                    &bull; не привязан к Telegram
                                </span>
                            </el-col>
                            <el-col :span="3">
                                {{ user.role }}
                            </el-col>
                            <el-col :span="4">
                                <span
                                    class="color-primary clickable"
                                    style="margin-right: 10px;"
                                    @click="edit(user)"
                                >
                                    Изменить
                                </span>
                                <span
                                    class="color-danger clickable"
                                    style="margin-right: 10px;"
                                    @click="doDelete(user)"
                                >
                                    Удалить
                                </span>
                            </el-col>
                        </el-row>
                    </el-card>
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
    import {formatDate, formatNumber} from '../../util/filters';
    import {mapGetters, mapActions} from 'vuex';
    import {errorHandler} from "../../util";

    export default {
        name: "Users",

        filters: {
            formatDate,
            formatNumber,
        },

        data() {
            return {
                loading: false,
            }
        },

        created() {
            this.fetchData();
        },

        computed: {
            ...mapGetters(['users']),
        },

        methods: {
            ...mapActions(['getUsers', "deleteUser"]),

            async fetchData() {
                this.loading = true;

                await this.getUsers()
                    .catch((err) => {
                        this.$message.error(errorHandler(err).message || 'Не удалось загрузить пользователя')
                    });

                this.loading = false;
            },

            add() {
                this.$router.push({
                    name: 'AddUser',
                });
            },

            view() {
                this.$router.push({
                    name: 'ViewUser',
                });
            },

            edit(user) {
                this.$router.push({
                    name: 'EditUser',
                    params: {user_id: user.id}
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
                    .deleteUser({id: item.id})
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
