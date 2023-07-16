var app_helper = {
    format_time(val){
        moment.locale('id')
        return moment(val).format("dddd, Do MMMM YYYY, HH:mm:ss")
    },
    implode_string(val,glue=' - '){
        return val.filter(str => (str != null && str != "")).join(glue);
    }
}

export {app_helper};