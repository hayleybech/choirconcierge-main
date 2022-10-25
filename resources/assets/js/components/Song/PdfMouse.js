import React, {useRef, useState} from 'react';
import {Document, Page} from 'react-pdf/dist/esm/entry.webpack';
import LoadingSpinner from "../LoadingSpinner";
import PdfToolbar from "./PdfToolbar";

const PdfMouse = ({ synth, filename, openFullscreen, closeFullscreen, isFullscreen, pitch }) => {
    const CONTAINER_PADDING = 0;
    const ZOOM_INTERVAL = 0.25;

    const [allPageNumbers, setAllPageNumbers] = useState([]);
    const [outerWidth, setOuterWidth] = useState(0);
    const [scale, setScale] = useState(1.0);

    const containerRef = useRef();

    const zoomIn = () => setScale((prevScale) => prevScale + ZOOM_INTERVAL);
    const zoomOut = () => setScale((prevScale) => prevScale - ZOOM_INTERVAL);

    function onDocumentLoadSuccess({ numPages }) {
        const allPageNumbers = [];
        for (let p = 1; p < numPages + 1; p++) {
            allPageNumbers.push(p);
        }
        setAllPageNumbers(allPageNumbers);

        setOuterWidth(containerRef.current.offsetWidth);
    }

    return (
        <div className="flex flex-col overflow-hidden h-full">

            <PdfToolbar isFullscreen={isFullscreen} closeFullscreen={closeFullscreen} openFullscreen={openFullscreen} zoomIn={zoomIn} zoomOut={zoomOut} pitch={pitch} synth={synth} />

            <div className="grow-0 h-full overflow-hidden">
                <div className="flex h-full w-full" style={{ padding: `${CONTAINER_PADDING}px` }} ref={containerRef}>

                    <Document
                        file={filename}
                        onLoadSuccess={onDocumentLoadSuccess}
                        className="w-full"
                        loading={<div className="m-8 text-xl"><LoadingSpinner /></div>}
                    >
                        <div className="w-full h-full overflow-scroll">
                            {allPageNumbers.map((pageNumber) => (
                                <Page
                                    key={pageNumber}
                                    pageNumber={pageNumber}
                                    width={outerWidth - CONTAINER_PADDING * 2}
                                    scale={scale}
                                    renderTextLayer={false}
                                    loading={null}
                                />
                            ))}
                        </div>
                    </Document>

                </div>
            </div>

        </div>
    );
};

export default PdfMouse;