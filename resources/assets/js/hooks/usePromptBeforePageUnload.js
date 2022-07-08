import {useEffect} from "react";

const usePromptBeforePageUnload = () => {
    useEffect(() => {
        window.addEventListener('beforeunload', promptBeforePageUnload);
        return () => {
            window.removeEventListener('beforeunload', promptBeforePageUnload);
        }
    }, []);
};

export default usePromptBeforePageUnload;

function promptBeforePageUnload(e) {
    e.preventDefault();

    return e.returnValue = 'Are you sure you want to exit/reload?';
}