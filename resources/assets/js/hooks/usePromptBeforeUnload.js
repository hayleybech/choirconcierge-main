import {useEffect} from "react";

const promptBeforeUnload = (e) => {
    e.preventDefault();

    return e.returnValue = 'Are you sure you want to exit/reload?';
}

const setupBeforeUnloadListener = (enabled) => {
    if(enabled) {
        window.onbeforeunload = (e) => promptBeforeUnload(e);
    } else {
        window.onbeforeunload = null;
    }
}

const usePromptBeforeUnload = (enabled) => {
    useEffect(() => {
        setupBeforeUnloadListener(enabled);

        return () => {
            window.onbeforeunload = null;
        }
    }, [enabled]);
};

export default usePromptBeforeUnload;