import Button from "../inputs/Button";
import Icon from "../Icon";
import PitchButton from "../PitchButton";
import React, {useState} from "react";
import PitchScale from "../PitchScale";

const PdfToolbar = ({ isFullscreen, closeFullscreen, openFullscreen, zoomIn, zoomOut, synth, pitch}) => {
    const [showPitchBar, setShowPitchBar] = useState(false);

    return (<>
        <div className="flex flex-wrap p-1.5 gap-x-2.5 border-b border-gray-300">
            <div className="flex gap-x-1">
                <Button onClick={() => zoomIn()} size="sm" className="h-7">
                    <Icon icon="search-plus" />
                </Button>
                <Button onClick={() => zoomOut()} size="sm" className="h-7">
                    <Icon icon="search-minus" />
                </Button>
            </div>

            <div className="flex gap-x-1">
                <PitchButton synth={synth} note={pitch} size="sm" className="h-7" />
                <Button onClick={() => setShowPitchBar((value) => !value)} size="sm" className="h-7">
                    <Icon icon="piano-keyboard" />
                </Button>
            </div>

            <Button variant="secondary" onClick={isFullscreen ? closeFullscreen : openFullscreen} size="sm" className="h-7 ml-auto">
                <Icon icon={isFullscreen ? 'times' : 'expand'} />
            </Button>
        </div>

        {showPitchBar && (
            <div className="flex flex-wrap p-1.5 gap-x-1 gap-y-1.5 border-b border-gray-300">
                <PitchScale synth={synth} root={pitch} />
            </div>
        )}
    </>
    );
};

export default PdfToolbar;