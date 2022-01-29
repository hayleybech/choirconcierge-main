import React, {useRef, useState} from 'react';
import {Document, Page} from 'react-pdf/dist/esm/entry.webpack';
import Button from "../inputs/Button";
import Icon from "../Icon";

const Pdf = ({ filename, openFullscreen, closeFullscreen, isFullscreen }) => {
    const CONTAINER_PADDING = 0;
    const PAGE_MAX_HEIGHT = 600;

    const [allPageNumbers, setAllPageNumbers] = useState([]);
    const [outerWidth, setOuterWidth] = useState(0);

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
        <div>

            <div className="p-2 space-x-2">
                {/*<Button onClick={() => setPageNumber((prevPage) => prevPage - 1)} disabled={pageNumber <= 1}>*/}
                {/*    <Icon icon="arrow-alt-left" />*/}
                {/*</Button>*/}
                {/*<Button onClick={() => setPageNumber((prevPage) => prevPage + 1)} disabled={pageNumber >= numPages}>*/}
                {/*    <Icon icon="arrow-alt-right" />*/}
                {/*</Button>*/}
                <Button variant="secondary" onClick={isFullscreen ? closeFullscreen : openFullscreen}>
                    <Icon icon={isFullscreen ? 'compress' : 'expand'} />
                </Button>
            </div>

            <div className="flex w-full" style={{ padding: `${CONTAINER_PADDING}px`}} ref={containerRef}>

                <Document file={filename} onLoadSuccess={onDocumentLoadSuccess}>
                    <div className="overflow-x-hidden overflow-y-auto h-full">
                        {allPageNumbers.map((pageNumber) => (
                            <Page key={pageNumber} pageNumber={pageNumber} width={outerWidth - CONTAINER_PADDING * 2} />
                        ))}
                    </div>
                </Document>

            </div>

        </div>
    );
};

export default Pdf;