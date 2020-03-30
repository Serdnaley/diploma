import moment from 'moment';

export function formatDate(date, format = 'DD.MM.YYYY') {
    return moment(date).format(format);
}

export function formatNumber(value) {

    switch (typeof value) {
        case 'number': return new Intl.NumberFormat('ru-RU').format(value);
        case 'string': return new Intl.NumberFormat('ru-RU').format(isNaN(Number(value)) ? 0 : Number(value));
        default: return 0;
    }

}