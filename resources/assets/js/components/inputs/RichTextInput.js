import React from 'react';
import ReactQuill from "react-quill";
import 'react-quill/dist/quill.snow.css';
import {nl2br} from "../../util";

const RichTextInput = ({ value, updateFn }) => (
    <ReactQuill value={nl2br(value)} onChange={updateFn} theme="snow" />
);

export default RichTextInput;