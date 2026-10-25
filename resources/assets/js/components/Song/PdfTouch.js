import React, { useRef, useState } from 'react';
import { Document, Page, pdfjs } from 'react-pdf/dist/esm/entry';
import pdfWorker from 'pdfjs-dist/legacy/build/pdf.worker.min.js?url';
import LoadingSpinner from '../LoadingSpinner';
import { TransformComponent, TransformWrapper } from 'react-zoom-pan-pinch';
import PdfToolbar from './PdfToolbar';

pdfjs.GlobalWorkerOptions.workerSrc = pdfWorker;

const PdfTouch = ({ instrument, setInstrument, filename, openFullscreen, closeFullscreen, isFullscreen, pitch }) => {
	const CONTAINER_PADDING = 0;

	const [allPageNumbers, setAllPageNumbers] = useState([]);
	const [outerWidth, setOuterWidth] = useState(0);
	const [refreshKey, setRefreshKey] = useState(1);

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
		<TransformWrapper key={refreshKey}>
			{({ zoomIn, zoomOut }) => (
				<div className="flex flex-col overflow-hidden h-full">
					<PdfToolbar
						isFullscreen={isFullscreen}
						closeFullscreen={closeFullscreen}
						openFullscreen={openFullscreen}
						zoomIn={zoomIn}
						zoomOut={zoomOut}
						pitch={pitch}
						instrument={instrument}
						setInstrument={setInstrument}
					/>

					<div className="grow-0 h-full overflow-hidden">
						<div
							className="flex h-full w-full"
							style={{ padding: `${CONTAINER_PADDING}px` }}
							ref={containerRef}
						>
							<TransformComponent wrapperStyle={{ height: '100%', width: '100%' }}>
								<Document
									file={filename}
									onLoadSuccess={onDocumentLoadSuccess}
									className="w-full"
									loading={
										<div className="m-8 text-xl">
											<LoadingSpinner />
										</div>
									}
								>
									{allPageNumbers.map(pageNumber => (
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

export default PdfTouch;
