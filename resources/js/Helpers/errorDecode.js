
export default{
    decodeError: (data) =>{
        if (data.redirect) {
            localStorage.token = null
            return window.location.href  = data.redirect

        }

        return data
    }
}
