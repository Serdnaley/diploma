<template>
    <route-modal
        :back-to-default="{name: 'Users'}"
        :title="(user_id ? 'Редагувати' : 'Створити') + ' користувача'"
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
                label="Ім'я:"
                prop="first_name"
            >
                <el-input
                    v-model="user_clone.first_name"
                    name="first_name"
                />
            </el-form-item>

            <el-form-item
                label="Прізвище:"
                prop="last_name"
            >
                <el-input
                    v-model="user_clone.last_name"
                    name="last_name"
                />
            </el-form-item>

            <el-form-item
                label="По батькові:"
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
                    :disabled="!$auth.check(['admin', 'manager']) || $auth.user().id === user_clone.id"
                >
                    <el-option
                        value="admin"
                        label="Адміністратор"
                    />
                    <el-option
                        value="user"
                        label="Користувач"
                    />
                </el-select>
            </el-form-item>

            <el-form-item
                label="Циклова комісія:"
                prop="user_category_id"
            >
                <remote-select
                    v-model="user_clone.user_category_id"
                    name="user_category_id"
                    :method="getCategories"
                    style="width: 100%;"
                    :disabled="!$auth.check(['admin', 'manager'])"
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
                    placeholder="Не прив'язаний"
                    :disabled="!$auth.check(['admin', 'manager'])"
                >
                    <template v-slot:item="{item}">
                        <el-option
                            :value="+item.id"
                            :label="item.name"
                        />
                    </template>
                </remote-select>
            </el-form-item>

            <el-form-item
                label="Пароль:"
                prop="password"
            >
                <div class="color-text-secondary">
                    Залиште порожнім якщо не хочете змінювати
                </div>
                <el-input
                    type="password"
                    v-model="user_clone.password"
                    name="password"
                    autocomplete="new-password"
                />
            </el-form-item>

            <el-collapse-transition>
                <el-form-item
                    label="Повторіть пароль:"
                    prop="password_confirmation"
                    v-if="user_clone.password"
                >
                    <el-input
                        type="password"
                        v-model="user_clone.password_confirmation"
                        name="password_confirmation"
                        autocomplete="new-password"
                    />
                </el-form-item>
            </el-collapse-transition>

            <el-form-item
                label="Токен швидкої авторизацій:"
                prop="fast_auth_token"
            >
                <el-input
                    type="fast_auth_token"
                    v-model="user_clone.fast_auth_token"
                    name="fast_auth_token"
                    readonly
                />
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
                    {{ user_id ? 'Зберегти' : 'Створити' }}
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
                            message: 'Введіть ім\'я користувача',
                            trigger: 'blur',
                        },
                        {
                            min: 2,
                            message: 'Занадто короткий ім\'я',
                            trigger: 'blur',
                        },
                    ],
                    last_name: [
                        {
                            required: true,
                            message: 'Введіть прізвище користувача',
                            trigger: 'blur',
                        },
                        {
                            min: 2,
                            message: 'Занадто коротка прізвище',
                            trigger: 'blur',
                        },
                    ],
                    patronymic: [
                        {
                            required: true,
                            message: 'Введіть по батькові користувача',
                            trigger: 'blur',
                        },
                        {
                            min: 2,
                            message: 'Занадто короткий батькові',
                            trigger: 'blur',
                        },
                    ],
                    role: [
                        {
                            required: true,
                            message: 'Виберіть роль користувача',
                            trigger: 'blur',
                        },
                    ],
                    email: [
                        {
                            required: true,
                            message: 'Введіть email користувача',
                            trigger: 'blur',
                        },
                        {
                            type: 'email',
                            message: 'Невірно введений email',
                            trigger: 'blur',
                        },
                    ],
                    password: [
                        {
                            type: 'string',
                            min: 6,
                            message: 'Пароль не може бути коротшим за 6 символів',
                            trigger: 'blur',
                        },
                    ],
                    password_confirmation: [
                        {
                            validator: (rule, val, cb) => {
                                let {password} = this.user_clone;
                                if (password && password !== val) {
                                    cb(new Error('Паролі не співпадають'));
                                }
                            },
                            message: 'Пароль не може бути коротшим за 6 символів',
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
                                || 'Не вдалося завантажити комісію'
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
                        title: 'Невірно введені дані',
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
                        this.$message.error(error.message || 'Не вдалося зберегти користувача');
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
