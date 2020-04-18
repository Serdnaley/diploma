<template>
    <route-modal
        :back-to-default="{name: 'Users'}"
        :title="(user_id ? 'Редактировать' : 'Добавить') + ' комиссию'"
        width="30%"
        @close="handleClose()"
        ref="modal"
    >
        <el-form
            ref="form"
            :model="user_clone"
            :rules="rules"
            v-loading="loading"
            @keyup.enter="submit()"
        >

            <el-form-item
                label="Имя:"
                prop="first_name"
            >
                <el-input
                    v-model="user_clone.first_name"
                    name="first_name"
                />
            </el-form-item>

            <el-form-item
                label="Фамилия:"
                prop="last_name"
            >
                <el-input
                    v-model="user_clone.last_name"
                    name="last_name"
                />
            </el-form-item>

            <el-form-item
                label="Отчество:"
                prop="patronymic"
            >
                <el-input
                    v-model="user_clone.patronymic"
                    name="patronymic"
                />
            </el-form-item>

            <el-form-item
                label="Email:"
                prop="email"
            >
                <el-input
                    type="email"
                    v-model="user_clone.email"
                    name="email"
                />
            </el-form-item>

            <el-form-item
                label="Роль:"
                prop="role"
            >
                <el-select
                    v-model="user_clone.role"
                    name="role"
                    style="width: 100%;"
                >
                    <el-option
                        value="admin"
                        label="Администратор"
                    />
                    <el-option
                        value="user"
                        label="Пользователь"
                    />
                </el-select>
            </el-form-item>

            <el-form-item
                label="Цикловая комиссия:"
                prop="user_category_id"
            >
                <remote-select
                    v-model="user_clone.user_category_id"
                    name="user_category_id"
                    :method="getCategories"
                    style="width: 100%;"
                >
                    <template v-slot:item="{item}">
                        <el-option
                            :value="item.id"
                            :label="item.name"
                        />
                    </template>
                </remote-select>
            </el-form-item>

            <el-form-item
                label="Telegram:"
                prop="telegram_chat_id"
            >
                <remote-select
                    v-model="user_clone.telegram_chat_id"
                    name="user_category_id"
                    :method="getTelegramChats"
                    clearable
                    style="width: 100%;"
                >
                    <template v-slot:item="{item}">
                        <el-option
                            :value="+item.id"
                            :label="item.name"
                        />
                    </template>
                </remote-select>
            </el-form-item>

            <ul v-if="errors" class="color-danger">
                <li v-for="error in errors">
                    {{ error[0] }}
                </li>
            </ul>

            <el-form-item>
                <el-button
                    type="primary"
                    @click="submit()"
                >
                    {{ user_id ? 'Сохранить' : 'Добавить' }}
                </el-button>
            </el-form-item>

        </el-form>
    </route-modal>
</template>

<script>
    import RouteModal from "../../components/RouteModal";
    import RemoteSelect from "../../components/RemoteSelect";
    import {mapGetters, mapActions} from 'vuex';
    import {validateForm, resetForm, errorHandler} from "../../util";

    export default {
        name: "AddEditUser",

        components: {
            RouteModal,
            RemoteSelect,
        },

        data() {
            return {
                loading: false,

                user_clone: {},

                user_default: {
                    first_name: '',
                    last_name: '',
                    patronymic: '',
                    role: 'user',
                    email: '',
                    user_category_id: '',
                },

                rules: {
                    first_name: [
                        {
                            required: true,
                            message: 'Введите имя пользователя',
                            trigger: 'blur',
                        },
                        {
                            min: 2,
                            message: 'Слишком короткое имя',
                            trigger: 'blur',
                        },
                    ],
                    last_name: [
                        {
                            required: true,
                            message: 'Введите фамилию пользователя',
                            trigger: 'blur',
                        },
                        {
                            min: 2,
                            message: 'Слишком короткая фамилия',
                            trigger: 'blur',
                        },
                    ],
                    patronymic: [
                        {
                            required: true,
                            message: 'Введите отчество пользователя',
                            trigger: 'blur',
                        },
                        {
                            min: 2,
                            message: 'Слишком короткое отчество',
                            trigger: 'blur',
                        },
                    ],
                    role: [
                        {
                            required: true,
                            message: 'Выберите роль пользователя',
                            trigger: 'blur',
                        },
                    ],
                    email: [
                        {
                            required: true,
                            message: 'Введите email пользователя',
                            trigger: 'blur',
                        },
                        {
                            type: 'email',
                            message: 'Неверно введен email',
                            trigger: 'blur',
                        },
                    ],
                },

                errors: false,
            }
        },

        mounted() {
            this.fetchData();
        },

        computed: {
            ...mapGetters(['user']),

            user_id() {
                return this.$route.params.user_id;
            },
        },

        watch: {
            user() {
                this.user_clone = _.cloneDeep(this.user);
            }
        },

        methods: {
            ...mapActions(['getUser', "updateUser", "addUser", "getCategories", "getTelegramChats"]),

            async fetchData() {
                this.loading = true;

                if (this.user_id) {
                    await this
                        .getUser({id: this.user_id})
                        .catch((err) => {
                            this.$message.error(
                                errorHandler(err).message
                                || 'Не удалось загрузить комиссию'
                            );
                        });
                } else {
                    this.user_clone = _.cloneDeep(this.user_default);
                }

                this.loading = false;
            },

            async submit() {

                if (!validateForm(this.$refs.form)) {
                    this.$notify({
                        title: 'Данные введены неверно',
                        type: 'error',
                        position: 'bottom-left',
                    });
                    return false;
                }

                this.loading = true;

                let method = this.user_id
                    ? this.updateUser
                    : this.addUser;

                let res = await method(this.user_clone)
                    .then(() => {
                        this.user_clone = _.cloneDeep(this.user_default);
                        this.close();
                    })
                    .catch((err) => {
                        let error = errorHandler(err);
                        this.errors = error;
                        this.$message.error(error.message || 'Не удалось сохранить пользователя');
                    });

                this.loading = false;

                return res;
            },

            close() {
                this.$refs.modal.close();
            },

            handleClose() {
                resetForm(this.$refs.form);
            },
        },
    }
</script>