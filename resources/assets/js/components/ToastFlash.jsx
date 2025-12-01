import React, {useEffect, useState} from 'react';
import ToastSuccess from "./ToastSuccess";
import ToastError from "./ToastError";

const delay = 3;

const ToastFlash = ({ errors, flash }) => {
    const hasErrors = errors && Object.keys(errors).length > 0;
    const hasMessage = !!flash?.message;

    const [show, setShow] = useState(hasErrors || hasMessage);

    useEffect(() => {
        setShow(hasErrors || hasMessage);

        let hideDelay = setTimeout(() => setShow(false), delay * 1000);

        return () => {
            clearTimeout(hideDelay);
        };
    }, [errors, flash]);

    if(hasErrors) {
        return <ToastError title="Form validation errors" show={show} close={() => setShow(false)} >
            <ul className="text-red-500 list-disc">
                {Object.values(errors).map((error) => <li key={error}>{error}</li>)}
            </ul>
        </ToastError>;
    }

    if(hasMessage) {
        return flash?.success
            ? <ToastSuccess show={show} close={() => setShow(false)} title={flash.message} />
            : <ToastError show={show} close={() => setShow(false)} title={flash.message} />;
    }

    return null;
};

export default ToastFlash;