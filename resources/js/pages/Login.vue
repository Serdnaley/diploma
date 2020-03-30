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

            <div v-show="auth_type === 'password'">
                <el-form-item
                    label="E-mail"
                    prop="email"
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
                >
                    <el-input
                        type="password"
                        name="password"
                        v-model="form.password"
                    />
                </el-form-item>
            </div>

            <div v-show="auth_type === 'token'">
                <el-form-item
                    label="Токен"
                    prop="token"
                >
                    <el-input
                        v-model="form.token"
                    />
                </el-form-item>
            </div>

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
            </el-form-item>

        </el-form>

    </el-dialog>
</template>

<script>
    import {validateForm, resetForm, errorHandler} from "../util";

    export default {
        name: 'Auth',

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
                loading: false,
                error: false,
            }
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

                if (!validateForm(this.$refs.form)) {
                    this.$notify({
                        title: 'Данные введены неверно',
                        type: 'error',
                        position: 'bottom-left',
                    });
                    return false;
                }

                this.loading = true;

                let res = await this.$auth
                    .login({
                        params: this.form
                    })
                    .catch((err) => {
                        this.error = errorHandler(err);
                    });

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