<template>
    <route-modal
        :back-to-default="{name: 'Reports'}"
        :title="(report_id ? 'Редагувати' : 'Створити') + ' звіт'"
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
                        label="Флюорографія"
                        name="fluorography"
                        style="margin: 0;"
                    />
                    <el-tab-pane
                        label="Медкомісія"
                        name="medical_board"
                        style="margin: 0;"
                    />
                </el-tabs>
            </el-form-item>

            <el-form-item
                label="Користувач:"
                prop="user_id"
            >
                <remote-select
                    v-model="report_clone.user_id"
                    :disabled="!$auth.check(['admin', 'manager'])"
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
                    :disabled="!$auth.check(['admin', 'manager'])"
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
                    Завантажте фото звіти:
                </div>

                <p>
                    <el-row class="fz-medium lh-primary">
                        Завантажено: {{ attachments_count }}
                        <span class="color-text-placeholder">(мінімум 2)</span>
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
                    {{ report_id ? 'Зберегти' : 'Створити' }}
                </el-button>
                <el-button
                    type="default"
                    @click="triggerUpload()"
                >
                    Завантажити фото
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
                    user_id: this.$auth.user().id,
                    type: 'fluorography',
                    date: moment().format('YYYY-MM-DD'),
                    attachment_ids: [],
                    attachments: [],
                },

                rules: {},

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
                                || 'Не вдалося загрузити відлік'
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
                        title: 'Дані введені невірно',
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
                        this.$message.error(error.message || 'Не вдалося зберегти звіт');
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
