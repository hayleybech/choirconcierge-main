import React from 'react';
import Button from "./Button";
import Icon from "../Icon";

const RichTextMenu = ({ editor }) => {
    if (!editor) {
        return null;
    }

    return (
        <div className="p-3">
            <Button
                variant="secondary"
                onClick={() => editor.chain().focus().toggleBold().run()}
                type="button"
                size="sm"
            >
                <Icon icon="bold" />
            </Button>
        </div>
    );
}

export default RichTextMenu;