import React, {useRef, useEffect, useState} from 'react';
import ButtonV2 from "./ButtonV2";
import classNames from "../../classNames";

const FileInput = ({ name, value, updateFn, hasErrors, multiple, vertical = false, otherProps }) => {
    const inputRef = useRef(null);
    const [filename, setFilename] = useState('');

    useEffect(() => {
        if(inputRef.current.files.length) {
            setFilename(Array.from(inputRef.current.files).map(file => file.name).join(', '));
        }
    }, [value]);

    return (
        <div className="mt-1">

            <div className={classNames('flex flex-wrap',
                vertical ? 'flex-col-reverse items-stretch text-center' : 'items-center space-x-4')}
            >
                <div className={classNames('text-sm text-gray-500', vertical ? 'mt-2' : '')}>
                    {filename || 'No file selected.'}
                </div>

                <ButtonV2
                    type="button"
                    size="md"
                    onClick={() => inputRef.current.click()}
                    className="grow"
                    intent={hasErrors ? 'danger' : 'secondary'}
                    weight="outline"
                >
                    Browse...
                </ButtonV2>
            </div>

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