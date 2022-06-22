import React from 'react';
import Button from "./Button";
import Icon from "../Icon";

const RichTextMenu = ({ editor }) => {
    if (!editor) {
        return null;
    }

    return (
        <div className="p-2 flex gap-x-3">
            <div>
                <MenuButton
                    title="Heading Level 1"
                    onClick={() => editor.chain().focus().toggleHeading({ level: 1 }).run()}
                    isActive={editor.isActive('heading', { level: 1 })}
                >
                    <Icon icon="h1" type="regular" />
                </MenuButton>

                <MenuButton
                    title="Heading Level 2"
                    onClick={() => editor.chain().focus().toggleHeading({ level: 2 }).run()}
                    isActive={editor.isActive('heading', { level: 2 })}
                >
                    <Icon icon="h2" type="regular" />
                </MenuButton>

                <MenuButton
                    title="Heading Level 3"
                    onClick={() => editor.chain().focus().toggleHeading({ level: 3 }).run()}
                    isActive={editor.isActive('heading', { level: 3 })}
                >
                    <Icon icon="h3" type="regular" />
                </MenuButton>
            </div>
            <div>
                <MenuButton title="Bold" onClick={() => editor.chain().focus().toggleBold().run()} isActive={editor.isActive('bold')}>
                    <Icon icon="bold" type="regular" />
                </MenuButton>

                <MenuButton title="Italic" onClick={() => editor.chain().focus().toggleItalic().run()} isActive={editor.isActive('italic')}>
                    <Icon icon="italic" type="regular" />
                </MenuButton>

                <MenuButton title="Underline" onClick={() => editor.chain().focus().toggleUnderline().run()} isActive={editor.isActive('underline')}>
                    <Icon icon="underline" type="regular" />
                </MenuButton>

                <MenuButton title="Strikethrough" onClick={() => editor.chain().focus().toggleStrike().run()} isActive={editor.isActive('strike')}>
                    <Icon icon="strikethrough" type="regular" />
                </MenuButton>
            </div>
            <div>
                <MenuButton title="Bullet List" onClick={() => editor.chain().focus().toggleBulletList().run()} isActive={editor.isActive('bulletList')}>
                    <Icon icon="list-ul" type="regular" />
                </MenuButton>
                <MenuButton title="Ordered List" onClick={() => editor.chain().focus().toggleOrderedList().run()} isActive={editor.isActive('orderedList')}>
                    <Icon icon="list-ol" type="regular" />
                </MenuButton>
            </div>
            <div>
                <MenuButton title="Horizontal Rule" onClick={() => editor.chain().focus().setHorizontalRule().run()}>
                    <Icon icon="horizontal-rule" type="regular" />
                </MenuButton>
            </div>
            <div>
                <MenuButton title="Clear Formatting" onClick={() => editor.chain().focus().unsetAllMarks().clearNodes().run()}>
                    <Icon icon="remove-format" type="regular" />
                </MenuButton>
            </div>
            <div>
                <MenuButton title="Undo" onClick={() => editor.chain().focus().undo().run()}>
                    <Icon icon="undo" type="regular" />
                </MenuButton>
                <MenuButton title="Redo" onClick={() => editor.chain().focus().redo().run()}>
                    <Icon icon="redo" type="regular" />
                </MenuButton>
            </div>
        </div>
    );
}

export default RichTextMenu;

const MenuButton = ({ onClick, isActive, title, children }) => (
    <Button
        variant="clear"
        onClick={onClick}
        type="button"
        size="sm"
        className={isActive ? 'text-purple-500' : ''}
        title={title}
    >
        { children }
    </Button>
);