<template>
    <div class="image-uploader" :class="{ 'hide-trigger': hideTrigger }">

        <el-collapse-transition>
            <p v-if="has_error">
                <el-alert
                    type="warning"
                    :closable="false"
                    show-icon
                    :title="too_large_message"
                />
            </p>
        </el-collapse-transition>

        <el-upload
            v-bind="{
                action: '',
                fileList: list,
                httpRequest: upload,
                listType: 'picture-card',
                multiple: true,
                accept: 'image/jpeg,image/jpg,image/png',
                onPreview: handlePictureCardPreview,
                ...$attrs
            }"
            v-on="$listeners"

            ref="uploader"
        >

            <template v-slot:file="{ file }">

                <img class="el-upload-list__item-thumbnail"
                     :src="file.url"
                     alt="">

                <transition name="fade">
                    <div v-show="showPreloader(file)"
                         @click="abort(file)"
                         class="el-upload-list__item-progress">
                        <el-progress
                            type="circle"
                            :format="() => '×'"
                            :width="50"
                            :percentage="file.percentage"
                        />
                    </div>
                </transition>

                <transition name="fade">
                    <span v-show="showActions(file)" class="el-upload-list__item-actions">
                        <span class="el-upload-list__item-preview"
                              @click="handlePictureCardPreview(file)">
                            <i class="el-icon-zoom-in"></i>
                        </span>
                        <span class="el-upload-list__item-delete"
                              @click="removeFile(file)">
                          <i class="el-icon-delete"></i>
                        </span>
                    </span>
                </transition>

                <transition name="fade">
                    <div v-show="showFail(file)"
                         class="el-upload-list__item-fail">
                        <p class="fz-medium">
                            Помилка
                            <el-tooltip :content="too_large_message">
                                <i class="far fa-question-circle"/>
                            </el-tooltip>
                        </p>
                        <el-button
                            size="medium"
                            @click="uploadAgain(file)"
                        >
                            Ще раз
                        </el-button>
                        <span></span>
                        <el-button
                            size="medium"
                            type="text"
                            @click="abort(file)"
                        >
                            Відміна
                        </el-button>
                    </div>
                </transition>

            </template>

            <i class="el-icon-plus"></i>
        </el-upload>

        <el-dialog :visible.sync="dialog_visible" append-to-body>
            <img width="100%" :src="dialog_img_url" alt="">
        </el-dialog>
    </div>
</template>

<script>
    import {mapActions} from 'vuex'
    import {errorHandler} from "../util";

    export default {
        name: 'ImageUploader',

        inheritAttrs: false,

        props: {
            list: {
                default: () => ([])
            },
            ids: {
                default: () => ([])
            },
            hideTrigger: {
                type: Boolean
            },
            category: {
                type: String
            },
        },

        data() {
            return {
                dialog_visible: false,
                dialog_img_url: '',
                uploader: false,
                file_ids: [],
                computed_list: [],
                tempIndex: 0,
                has_error: false,
                too_large_message: 'Файли повинні бути формату .png або .jpg і розмір не більше 5мб.',
            }
        },

        mounted() {
            this.uploader = this.$refs.uploader;
        },

        computed: {

            files() {
                if (!this.uploader) return [];
                return this.uploader.$data.uploadFiles || [];
            },

            loader() {
                for (let file of this.files) {
                    if (this.showPreloader(file)) {
                        return true;
                    }
                }

                return false;
            },

        },

        watch: {

            files: {
                handler() {
                    let ids = [];

                    for (let file of this.files) {
                        let id = file.id || _.at(file, 'response_data.data.id')[0];

                        if (id) ids.push(id);
                    }

                    this.file_ids = ids;
                    this.$emit('update:ids', ids);
                },
                deep: true,
            },

            list() {
                this.list.forEach(item => {
                    item.uid = item.uid || item.id || (Date.now() + this.tempIndex++)
                });
            },
        },

        methods: {
            ...mapActions(['uploadFile', 'downloadFile']),

            upload(file) {
                let realFile = _.find(this.files, { uid: file.file.uid });

                file.category = this.category;

                this
                    .uploadFile(file)
                    .then(res => {
                        realFile.status = 'success';
                        realFile.response_data = _.cloneDeep(res.data);
                        this.checkUnloaded();
                    })
                    .catch(err => {
                        if (realFile) {
                            this.has_error = true;
                            realFile.status = 'fail';
                            realFile.response_data = _.cloneDeep(errorHandler(err));
                        }
                    });
            },

            uploadAgain(file) {
                file.status = 'ready';
                file.percentage = 0;
                this.uploader.$refs["upload-inner"].post(file.raw);
            },

            triggerUpload() {
                this.uploader.$refs["upload-inner"].handleClick();
            },

            handlePictureCardPreview(file) {
                this.dialog_visible = true;
                this.dialog_img_url = file.url;
            },

            abort(file) {
                if (file.raw && typeof file.raw.cancelUpload === 'function') {
                    file.raw.cancelUpload();

                    let index = this.files.indexOf(file);

                    if (index !== -1) this.files.splice(index, 1);
                }
            },

            removeFile(file) {

                let index = this.files.indexOf(file);

                if (index !== -1) this.files.splice(index, 1);

                this.checkUnloaded();
            },

            checkUnloaded() {
                if (!this.loader && this.files.length) {
                    this.$emit('submit');
                }
            },

            showPreloader(file) {
                return ['ready', 'uploading'].includes(file.status);
            },

            showActions(file) {
                return ['success', '', undefined].includes(file.status);
            },

            showFail(file) {
                return ['fail'].includes(file.status);
            },
        },
    }
</script>

<style lang="scss">
    @import '../../scss/variables';

    .image-uploader {

        &.hide-trigger {
            .el-upload--picture-card {
                display: none;
            }
        }

        .el-upload {
            position: relative;
            margin-bottom: 10px;

            &-list {
                &__item {
                    height: 100px;
                    width: 100px;

                    &-thumbnail {
                        object-fit: cover;
                    }

                    &-progress {
                        position: absolute;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        display: flex;
                        justify-content: center;
                        align-items: center;

                        .el-progress {
                            width: auto;
                            cursor: pointer;

                            &-circle {
                                background: transparentize($--color-black, .25);
                                padding: 5px;
                                border-radius: 50%;
                                animation: rotate360 2s linear infinite;

                                &__track {
                                    stroke: transparent;
                                }

                                &__path {
                                    stroke: $--color-white;
                                }
                            }

                            &__text {
                                font-size: 35px !important;
                                color: $--color-white;
                            }
                        }
                    }

                    &-actions {

                    }

                    &-fail {
                        position: absolute;
                        top: 0;
                        left: 0;
                        height: 100%;
                        width: 100%;

                        padding: 10px;
                        box-sizing: border-box;

                        display: flex;
                        flex-direction: column;
                        justify-content: center;
                        align-items: center;

                        background: transparentize($--color-white, .1);
                        color: $--color-danger;
                        text-align: center;
                        font-weight: $--font-weight-primary;
                    }
                }
            }
        }
    }
</style>
