<template>
    <el-dialog
        :visible="true"
        width="25%"
        class="dialog-auth"
        :show-close="false"
    >

        <template v-slot:title>
            <el-tabs
                v-model="auth_type"
                class="stylized-tabs"
            >
                <el-tab-pane
                    name="password"
                    label="Логин/Пароль"
                    style="margin: 0;"
                />
                <el-tab-pane
                    name="token"
                    label="Токен"
                    style="margin: 0;"
                />
            </el-tabs>
        </template>

        <el-form
            ref="form"
            :model="form"
            :rules="rules"
            v-loading="loading"
            @keyup.enter="submit()"
        >

            <template v-if="auth_type === 'password'">
                <el-form-item
                    label="E-mail"
                    prop="email"
                    key="email"
                >
                    <el-input
                        type="email"
                        name="email"
                        v-model="form.email"
                    />
                </el-form-item>

                <el-form-item
                    label="Пароль"
                    prop="password"
                    key="password"
                >
                    <el-input
                        type="password"
                        name="password"
                        v-model="form.password"
                    />
                </el-form-item>
            </template>

            <template v-else>
                <el-form-item
                    label="Токен"
                    prop="token"
                    key="token"
                >
                    <el-input
                        name="token"
                        v-model="form.token"
                    />
                </el-form-item>
            </template>

            <el-form-item v-if="error">
                <el-alert
                    type="error"
                    :closable="false"
                    show-icon
                    title="Неверно введены данные."
                />
            </el-form-item>

            <el-form-item>
                <el-button
                    type="primary"
                    @click="submit()"
                >
                    Войти
                </el-button>
                <el-button
                    v-if="$auth.check() && $auth.user()"
                    type="text"
                    @click="$router.replace({ ...$route, query: null })"
                >
                    Продолжить как {{ $auth.user().short_name }}
                </el-button>
            </el-form-item>

        </el-form>

    </el-dialog>
</template>

<script>
    import {validateForm, errorHandler, getQueryVariable, setCookie} from "../util";

    export default {
        name: 'Login',

        props: {
            view: {
                type: Boolean
            }
        },

        data() {
            return {
                auth_type: 'password',
                form: {
                    token: '',
                    email: '',
                    password: '',
                },
                loading: true,
                error: false,
            }
        },

        created() {
            this.$nextTick(async () => {
                let token = getQueryVariable('auth_token');
                if (token) {
                    this.form.token = token;
                    this.auth_type = 'token';

                    await this
                        .submit()
                        .catch(() => {
                            this.$message.error('Автоматическая авторизация не удалась');
                        });

                }
                this.loading = false;
            });
        },

        computed: {
            rules() {
                return {
                    token: [
                        {
                            required: this.auth_type === 'token',
                            message: 'Введите токен',
                            trigger: 'blur',
                        },
                    ],
                    email: [
                        {
                            required: this.auth_type === 'password',
                            message: 'Введите ваш email',
                            trigger: 'blur',
                        },
                        {
                            type: 'email',
                            message: 'Введите правильный email',
                            trigger: 'blur',
                        },
                    ],
                    password: [
                        {
                            required: this.auth_type === 'password',
                            message: 'Введите пароль',
                            trigger: 'blur',
                        },
                        {
                            min: 6,
                            message: 'Пароль должен быть больше 6 символов',
                            trigger: 'blur',
                        },
                    ],
                }
            },
        },

        watch: {
            auth_type() {
                this.error = false;
            },
        },

        methods: {
            async submit() {

                this.loading = true;

                let res;

                if (this.auth_type === 'password') {

                    if (!validateForm(this.$refs.form)) {
                        this.$notify({
                            title: 'Данные введены неверно',
                            type: 'error',
                            position: 'bottom-left',
                        });
                        this.loading = false;
                        return false;
                    }

                    res = await this.$auth
                        .login({
                            params: this.form,
                        })
                        .catch((err) => {
                            this.error = errorHandler(err);
                        });
                } else {

                    res = await axios
                        .post('auth/fast_auth', {
                            auth_token: this.form.token
                        })
                        .catch((err) => {
                            this.error = errorHandler(err);
                        });

                    if (res.data.data) {

                        this.$auth.watch.data = res.data.data;
                        this.$auth.watch.authenticated = true;
                        this.$auth.watch.loaded = true;

                        setCookie('rememberMe', 'true', 12096e5);

                        await this.$router.push({name: 'Home'});
                    }
                }

                this.loading = false;

                return res;
            },
        }

    }
</script>

<style lang="scss">
    @import '../../scss/variables';

    .dialog-auth {
        background: $--color-white;
    }
</style>