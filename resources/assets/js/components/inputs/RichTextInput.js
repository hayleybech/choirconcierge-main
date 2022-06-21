import React from 'react';
import {nl2br} from "../../util";
import {EditorContent, useEditor} from "@tiptap/react";
import StarterKit from "@tiptap/starter-kit";

const RichTextInput = ({ value, updateFn }) => {
    const editor = useEditor({
        extensions: [StarterKit],
        content: nl2br(value),
        onUpdate({ editor }) {
            updateFn(editor.getHTML());
        }
    });

    return <EditorContent editor={editor} />;
}

export default RichTextInput;