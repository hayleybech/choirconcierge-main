import React, {useEffect, useState} from 'react';
import ToastError from "./ToastError";
import ToastSuccess from "./ToastSuccess";

const ToastFlash = ({ errors, flash }) => {
    const hasErrors = errors && Object.keys(errors).length > 0;
    const hasMessage = !!flash?.message;

    const [show, setShow] = useState(hasErrors || hasMessage);

    useEffect(() => {
        setShow(hasErrors || hasMessage);
    }, [errors, flash]);

    if(hasErrors) {
        return <ToastError show={show} close={() => setShow(false)} errors={errors} />;
    }

    if(hasMessage) {
        return <ToastSuccess show={show} close={() => setShow(false)} title={flash.message} />
    }

    return null;
};

export default ToastFlash;