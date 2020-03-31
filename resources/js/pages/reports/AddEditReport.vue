<template>
    <route-modal
        :back-to-default="{name: 'Reports'}"
        :title="(report_id ? 'Редактировать' : 'Добавить') + ' отчет'"
        width="30%"
        @close="handleClose()"
        ref="modal"
    >
        <el-form
            ref="form"
            :model="report_clone"
            :rules="rules"
            v-loading="loading"
            @keyup.enter="submit()"
        >

            <el-form-item>
                <el-tabs
                    v-model="report_clone.type"
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
            </el-form-item>

            <el-form-item
                label="Пользователь:"
                prop="user_id"
            >
                <remote-select
                    v-model="report_clone.user_id"
                    name="report_category_id"
                    :method="getUsers"
                    style="width: 100%;"
                >
                    <template v-slot:item="{item}">
                        <el-option
                            :value="item.id"
                            :label="item.full_name"
                        />
                    </template>
                </remote-select>
            </el-form-item>

            <el-form-item
                label="Дата прохождения:"
                prop="date"
            >
                <el-date-picker
                    v-model="report_clone.date"
                    format="dd.MM.yyyy"
                    value-format="yyyy-MM-dd"
                    name="date"
                    style="width: 100%;"
                />
            </el-form-item>

            <el-form-item
                prop="attachment_ids"
            >
                <div class="fz-large">
                    Загрузите фото отчеты:
                </div>

                <p>
                    <el-row class="fz-medium lh-primary">
                        Загружено: {{ attachments_count }}
                        <span class="color-text-placeholder">(минимум 2)</span>
                    </el-row>
                    <el-progress
                        :percentage="(attachments_count <= 2 ? attachments_count : 2) / 2 * 100"
                        :show-text="false"
                        class="photos-upload-progress"
                    />
                </p>
                <image-uploader
                    :ids.sync="report_clone.attachment_ids"
                    :list="report_clone.attachments"
                    :accept="accept"
                    category="shift-open"
                    hide-trigger
                    ref="uploader"
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
                    :disabled="attachments_count < 2"
                >
                    {{ report_id ? 'Сохранить' : 'Добавить' }}
                </el-button>
                <el-button
                    type="default"
                    @click="triggerUpload()"
                >
                    Прикрепить фото
                </el-button>
            </el-form-item>

        </el-form>
    </route-modal>
</template>

<script>
    import RouteModal from "../../components/RouteModal";
    import RemoteSelect from "../../components/RemoteSelect";
    import ImageUploader from "../../components/ImageUploader";
    import {mapGetters, mapActions} from 'vuex';
    import {validateForm, resetForm, errorHandler} from "../../util";
    import moment from 'moment';

    export default {
        name: "AddEditReport",

        components: {
            RouteModal,
            RemoteSelect,
            ImageUploader,
        },

        data() {
            return {
                loading: false,

                report_clone: {},

                report_default: {
                    user_id: null,
                    type: 'fluorography',
                    date: moment().format('YYYY-MM-DD'),
                    attachment_ids: [],
                    attachments: [],
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
                    phone: [
                        {
                            required: true,
                            message: 'Введите номер телефона пользователя',
                            trigger: 'blur',
                        },
                        {
                            min: 10,
                            message: 'Неверно введен номер телефона',
                            trigger: 'blur',
                        },
                    ],
                },

                clicked: false,
                accept: 'image/jpeg,image/jpg,image/png',

                errors: false,
            }
        },

        mounted() {
            this.fetchData();

            this.$nextTick(() => {
                this.$refs["uploader"]
                    .$refs["uploader"]
                    .$refs["upload-inner"]
                    .$refs["input"]
                    .addEventListener('change', () => this.clicked = false);
            })
        },

        computed: {
            ...mapGetters(['report']),

            report_id() {
                return this.$route.params.report_id;
            },

            attachments_count() {
                if (_.at(this, 'report_clone.attachment_ids.length')[0]) {
                    return this.report_clone.attachment_ids.length;
                } else {
                    return 0;
                }
            },
        },

        watch: {
            report() {
                this.report_clone = _.cloneDeep(this.report);
            }
        },

        methods: {
            ...mapActions(['getReport', "updateReport", "addReport", "getUsers"]),

            async fetchData() {
                this.loading = true;

                if (this.report_id) {
                    await this
                        .getReport({id: this.report_id})
                        .catch((err) => {
                            this.$message.error(
                                errorHandler(err).message
                                || 'Не удалось загрузить отчет'
                            );
                        });
                } else {
                    this.report_clone = _.cloneDeep(this.report_default);
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

                let method = this.report_id
                    ? this.updateReport
                    : this.addReport;

                let res = await method(this.report_clone)
                    .then(() => {
                        this.report_clone = _.cloneDeep(this.report_default);
                        this.close();
                    })
                    .catch((err) => {
                        let error = errorHandler(err);
                        this.errors = error;
                        this.$message.error(error.message || 'Не удалось сохранить отчет');
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

            triggerUpload() {
                let triggerUpload = _.at(this, '$refs.uploader.triggerUpload')[0];
                if (typeof triggerUpload === 'function') {
                    if (!this.clicked) {
                        this.$set(this, 'clicked', true);
                    } else {
                        this.$set(this, 'clicked', false);
                        this.$set(this, 'accept', '');
                    }

                    this.$nextTick(() => triggerUpload());
                }
            },
        },
    }
</script>