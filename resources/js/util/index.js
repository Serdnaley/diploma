

export function validateForm(form) {
    let res = true;
    form.validate((valid) => {
        if (!valid) res = false;
    });
    return res;
}


export function resetForm(form) {
    form.resetFields();
}


export function errorHandler(err = false)
{

    if (err && process.env.NODE_ENV === 'development') {
        console.log(err.response);
    }

    if (
        err &&
        err.response &&
        err.response.status === 422 &&
        err.response.data
    ) {
        return err.response.data.errors || err.response.data
    } else {
        return false;
    }

}