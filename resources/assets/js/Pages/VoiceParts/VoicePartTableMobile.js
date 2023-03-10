import React from 'react';
import Swatch from "../../components/Swatch";
import TableMobile, {TableMobileItem} from "../../components/TableMobile";
import useRoute from "../../hooks/useRoute";

const VoicePartTableMobile = ({ voiceParts }) => {
    const { route } = useRoute();

    return (
        <TableMobile>
            {voiceParts.map((voicePart) => (
                <TableMobileItem key={voicePart.id} url={route('voice-parts.edit', {voice_part: voicePart.id})}>
                    <div className="shrink-0">
                        <Swatch colour={voicePart.colour} />
                    </div>
                    <div className="min-w-0 flex-1 px-4 lg:grid lg:grid-cols-2 lg:gap-4">
                        <div>
                            <div className="flex items-center justify-between">
                                <p className="flex items-center min-w-0 mr-1.5">
                                    <span className="text-sm font-medium text-purple-600 truncate">{voicePart.title}</span>
                                </p>
                                <p className="flex items-center min-w-0 mr-1.5">
                                <span className="text-sm font-medium text-sm text-gray-500">
                                    {voicePart.singers_count} {voicePart.singers_count === 1 ? 'singer' : 'singers'}
                                </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </TableMobileItem>
            ))}
        </TableMobile>
    );
}

export default VoicePartTableMobile;