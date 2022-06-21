import React from 'react';
import Button from "./Button";
import Icon from "../Icon";

const RichTextMenu = ({ editor }) => {
    if (!editor) {
        return null;
    }

    return (
        <div className="p-2">
            <MenuButton onClick={() => editor.chain().focus().toggleBold().run()} isActive={editor.isActive('bold')}>
                <Icon icon="bold" />
            </MenuButton>

            <MenuButton onClick={() => editor.chain().focus().toggleItalic().run()} isActive={editor.isActive('italic')}>
                <Icon icon="italic" />
            </MenuButton>

            <MenuButton onClick={() => editor.chain().focus().toggleUnderline().run()} isActive={editor.isActive('underline')}>
                <Icon icon="underline" />
            </MenuButton>

            <MenuButton onClick={() => editor.chain().focus().toggleStrike().run()} isActive={editor.isActive('strike')}>
                <Icon icon="strikethrough" />
            </MenuButton>
        </div>
    );
}

export default RichTextMenu;

const MenuButton = ({ onClick, isActive, children }) => (
    <Button
        variant="clear"
        onClick={onClick}
        type="button"
        size="sm"
        className={isActive ? 'text-purple-500' : ''}
    >
        { children }
    </Button>
);