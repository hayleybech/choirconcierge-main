import React from 'react';
import {nl2br} from "../../util";
import {EditorContent, useEditor} from "@tiptap/react";
import StarterKit from "@tiptap/starter-kit";
import RichTextMenu from "./RichTextMenu";
import {Underline} from "@tiptap/extension-underline";
import {Link} from "@tiptap/extension-link";

const RichTextInput = ({ value, updateFn, max = 5000 }) => {
    const editor = useEditor({
        extensions: [
            StarterKit,
            Underline,
            Link.configure({
                HTMLAttributes: {
                    class: 'text-purple-700',
                },
                openOnClick: false,
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

    let charsUsed = editor?.getHTML()?.length ?? 0;
    return (
        <div className="shadow-sm rounded-md border border-gray-300 ring-1 ring-transparent focus-within:ring-purple-500 focus-within:border-purple-500">
            <RichTextMenu editor={editor} />
            <EditorContent editor={editor} className="" />

            <div className={`text-xs py-0.5 px-2 ${charsUsed >= max ? 'text-red-500' : 'text-gray-500'}`}>
                {charsUsed} / {max} (Includes HTML)
            </div>
        </div>
    );
}

export default RichTextInput;