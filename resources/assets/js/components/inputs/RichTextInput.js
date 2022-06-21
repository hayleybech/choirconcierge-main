import React from 'react';
import {nl2br} from "../../util";
import {EditorContent, useEditor} from "@tiptap/react";
import StarterKit from "@tiptap/starter-kit";
import RichTextMenu from "./RichTextMenu";
import {Underline} from "@tiptap/extension-underline";
import {Link} from "@tiptap/extension-link";

const RichTextInput = ({ value, updateFn }) => {
    const editor = useEditor({
        extensions: [
            StarterKit,
            Underline,
            Link.configure({
                HTMLAttributes: {
                    class: 'text-purple-700',
                },
            }),
        ],
        content: nl2br(value),
        onUpdate({ editor }) {
            updateFn(editor.getHTML());
        },
        editorProps: {
            attributes: {
                class: 'prose sm:max-w-none p-4 bg-white focus:outline-none rounded-bl-md rounded-br-md'
            }
        }
    });

    return (
        <div className="shadow-sm rounded-md border border-gray-300 ring-1 ring-transparent focus-within:ring-purple-500 focus-within:border-purple-500">
            <RichTextMenu editor={editor} />
            <EditorContent editor={editor} className="" />
        </div>
    );
}

export default RichTextInput;