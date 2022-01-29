import React, {useRef, useState} from 'react';
import {Document, Page} from 'react-pdf/dist/esm/entry.webpack';
import Button from "../inputs/Button";
import Icon from "../Icon";

const Pdf = ({ filename, openFullscreen, closeFullscreen, isFullscreen }) => {
    const CONTAINER_PADDING = 0;
    const ZOOM_INTERVAL = 0.25;

    const [allPageNumbers, setAllPageNumbers] = useState([]);
    const [outerWidth, setOuterWidth] = useState(0);
    const [scale, setScale] = useState(1.0);

    const containerRef = useRef();

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

            <div className="p-2 space-x-2 border-b border-gray-300">
                <Button onClick={() => setScale((prevScale) => prevScale + ZOOM_INTERVAL)}>
                    <Icon icon="search-plus" />
                </Button>
                <Button onClick={() => setScale((prevScale) => prevScale - ZOOM_INTERVAL)}>
                    <Icon icon="search-minus" />
                </Button>
                <Button variant="secondary" onClick={isFullscreen ? closeFullscreen : openFullscreen}>
                    <Icon icon={isFullscreen ? 'compress' : 'expand'} />
                </Button>
            </div>
            
            <div className="flex-grow-0 h-full overflow-hidden">
                <div className="flex h-full w-full" style={{ padding: `${CONTAINER_PADDING}px`}} ref={containerRef}>

                    <Document file={filename} onLoadSuccess={onDocumentLoadSuccess} className="w-full">
                        <div className="w-full h-full overflow-scroll">
                            {allPageNumbers.map((pageNumber) => (
                                <Page
                                    key={pageNumber}
                                    pageNumber={pageNumber}
                                    width={outerWidth - CONTAINER_PADDING * 2}
                                    scale={scale}
                                    renderTextLayer={false}
                                />
                            ))}
                        </div>
                    </Document>

                </div>
            </div>


        </div>
    );
};

export default Pdf;