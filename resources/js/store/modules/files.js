export default {

    state: {},

    getters: {},

    mutations: {},

    actions: {

        async uploadFile(noop, data) {

            const cancelSource = axios.CancelToken.source();
            const formData = new FormData();

            data.file.cancelUpload = cancelSource.cancel;

            formData.append('file', data.file);
            formData.append('category', data.category);

            return axios({
                method: 'POST',
                url: '/file/store',
                data: formData,

                headers: {
                    'Content-Type': 'multipart/form-data'
                },

                onUploadProgress(e) {
                    if (e.total > 0) {
                        e.percent = e.loaded / e.total * 100;
                    }
                    data.onProgress(e);
                },

                cancelToken: cancelSource.token
            });
        },
    }
};
