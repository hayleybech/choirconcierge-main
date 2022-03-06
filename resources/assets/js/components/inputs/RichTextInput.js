import React from 'react';
import ReactQuill from "react-quill";
import 'react-quill/dist/quill.snow.css';

const RichTextInput = ({ value, updateFn }) => (
    <ReactQuill value={value} onChange={updateFn} theme="snow" />
);

export default RichTextInput;