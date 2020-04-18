

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
        console.log(err, err.response);
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


export function queryString(query = []) {
    const items = Object.keys(query);

    let url = '';

    items.forEach((key, i) => {
        url += (i === 0 ? '' : '&') + encodeURIComponent(key) + '=' + encodeURIComponent(query[key]);
    });

    return url;
}


export function getQueryVariable(variable) {
    let query = window.location.search.substring(1);
    let vars = query.split('&');

    for (let i = 0; i < vars.length; i++) {
        let pair = vars[i].split('=');
        if (decodeURIComponent(pair[0]) === variable) {
            return decodeURIComponent(pair[1]);
        }
    }

    return undefined;
}