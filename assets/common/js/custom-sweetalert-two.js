class CustomSweetalertTwo {
    static success(msg = 'Item Added', text = ""){
        return Swal.fire({
            position: 'top-end',
            icon: 'success',
            title: msg,
            text: text,
            showConfirmButton: false,
            timer: 2000,
            toast: true,
        })
    }

    static error(msg = 'Something went wrong', text = ""){
        return Swal.fire({
            position: 'top-end',
            icon: 'error',
            title: msg,
            text: text,
            showConfirmButton: false,
            timer: 2000,
            toast: true,
        })
    }

    static warning(msg = 'Notice something', text = "")
    {
        return Swal.fire({
            position: 'top-end',
            icon: 'warning',
            title: msg,
            text: text,
            showConfirmButton: false,
            timer: 2000,
            toast: true,
        })
    }
}