import React, {useEffect, useRef, useState} from 'react';
import {Document, Page} from 'react-pdf/dist/esm/entry.webpack';
import Button from "../inputs/Button";
import Icon from "../Icon";
import LoadingSpinner from "../LoadingSpinner";
import {TransformComponent, TransformWrapper} from "react-zoom-pan-pinch";
import PitchButton from "../PitchButton";

const Pdf = ({ filename, openFullscreen, closeFullscreen, isFullscreen, pitch }) => {
    const CONTAINER_PADDING = 0;

    const [allPageNumbers, setAllPageNumbers] = useState([]);
    const [outerWidth, setOuterWidth] = useState(0);
    const [refreshKey, setRefreshKey] = useState(1);

    const containerRef = useRef();

    function forceRefresh() {
        setRefreshKey((prevKey) => prevKey + 1);
    }

    useEffect(() => {
        window.addEventListener('orientationchange', forceRefresh);
        return () => {
            window.removeEventListener('orientationchange', forceRefresh);
        }
    }, []);

    function onDocumentLoadSuccess({ numPages }) {
        const allPageNumbers = [];
        for (let p = 1; p < numPages + 1; p++) {
            allPageNumbers.push(p);
        }
        setAllPageNumbers(allPageNumbers);

        setOuterWidth(containerRef.current.offsetWidth);
    }

    return (
        <TransformWrapper key={refreshKey}>
            {({ zoomIn, zoomOut }) => (
                <div className="flex flex-col overflow-hidden h-full">

                    <div className="flex p-1.5 space-x-2.5 border-b border-gray-300">
                        <Button variant="secondary" onClick={isFullscreen ? closeFullscreen : openFullscreen} size="sm" className="h-7">
                            <Icon icon={isFullscreen ? 'compress' : 'expand'} />
                        </Button>
                        <div className="space-x-1">
                            <Button onClick={() => zoomIn()} size="sm" className="h-7">
                                <Icon icon="search-plus" />
                            </Button>
                            <Button onClick={() => zoomOut()} size="sm" className="h-7">
                                <Icon icon="search-minus" />
                            </Button>
                        </div>
                        <PitchButton note={pitch} size="sm" className="h-7" />
                    </div>

                    <div className="grow-0 h-full overflow-hidden">
                        <div className="flex h-full w-full" style={{ padding: `${CONTAINER_PADDING}px` }} ref={containerRef}>

                            <TransformComponent wrapperStyle={{ height: "100%" }}>
                                <Document
                                    file={filename}
                                    onLoadSuccess={onDocumentLoadSuccess}
                                    className="w-full"
                                    loading={<div className="m-8 text-xl"><LoadingSpinner /></div>}
                                >
                                    {allPageNumbers.map((pageNumber) => (
                                        <Page
                                            key={pageNumber}
                                            pageNumber={pageNumber}
                                            width={outerWidth - CONTAINER_PADDING * 2}
                                            scale={1.0}
                                            renderTextLayer={false}
                                            loading={null}
                                        />
                                    ))}
                                </Document>
                            </TransformComponent>

                        </div>
                    </div>

                </div>
            )}
        </TransformWrapper>
    );
};

export default Pdf;