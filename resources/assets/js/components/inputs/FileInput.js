import React, {useRef, useEffect, useState} from 'react';
import Button from "./Button";

const FileInput = ({name, type = 'text', value, updateFn, hasErrors, multiple, otherProps}) => {
    const inputRef = useRef(null);
    const [filename, setFilename] = useState('');

    useEffect(() => {
        if(inputRef.current.files.length) {
            setFilename(Array.from(inputRef.current.files).map(file => file.name).join(', '));
        }
    }, [value]);

    return (
        <div className="mt-1">

            <Button
                size="sm"
                onClick={() => inputRef.current.click()}
                className="w-full"
                variant={hasErrors ? 'danger-outline' : 'secondary'}
            >
                Browse...
            </Button>

            <p className="text-sm text-gray-500">{filename}</p>

            <input
                type="file"
                id={name}
                name={name}
                className="hidden"
                ref={inputRef}
                onChange={e => updateFn(e.target.files)}
                multiple={multiple}
                {...otherProps}
            />
        </div>
    );
}

export default FileInput;