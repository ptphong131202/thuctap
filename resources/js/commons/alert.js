const Notiflix = require('notiflix/src/notiflix');

module.exports = {
    Comfirm: function (title, message, yesCallback, noCallback) {
        Notiflix.Confirm.Show(title, message, 'Đồng ý', 'Hủy bỏ', yesCallback, noCallback);
    },
    Loading: {
        Load: function (message) {
            Notiflix.Loading.Dots(message);
        },
        Remove: function (millisecond) {
            Notiflix.Loading.Remove(millisecond);
        },
        Block: function (selector, message) {
            Notiflix.Block.Circle(selector, message);
        },
        Unblock: function (selector) {
            Notiflix.Block.Remove(selector);
        }
    },
    Notify: {
        Success: function (message, callback) {
            Notiflix.Notify.Success(message, callback);
        },
        Failure: function (message, callback) {
            Notiflix.Notify.Failure(message, callback);
        },
        Info: function (message, callback) {
            Notiflix.Notify.Info(message, callback);
        },
        Warning: function (message, callback) {
            Notiflix.Notify.Warning(message, callback);
        },
    },
    Report: {
        Success: function (title, message, buttonLabel, callback) {
            Notiflix.Report.Success(title, message, buttonLabel, callback);
        },
        Failure: function (title, message, buttonLabel, callback) {
            Notiflix.Report.Failure(title, message, buttonLabel, callback);
        },
        Info: function (title, message, buttonLabel, callback) {
            Notiflix.Report.Info(title, message, buttonLabel, callback);
        },
        Warning: function (message, callback) {
            Notiflix.Report.Warning(title, message, buttonLabel, callback);
        },
    },
}