import React from 'react';
import PdfTouch from "./PdfTouch";
import PdfMouse from "./PdfMouse";
import {primaryInput} from "detect-it";

const Pdf = ({ ...props }) => (
    primaryInput === 'touch' ? <PdfTouch {...props} /> : <PdfMouse {...props} />
);

export default Pdf;