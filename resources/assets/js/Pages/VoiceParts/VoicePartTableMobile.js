import React from 'react';
import Swatch from "../../components/Swatch";
import TableMobile, {TableMobileItem} from "../../components/TableMobile";

const VoicePartTableMobile = ({ voiceParts }) => (
    <TableMobile>
        {voiceParts.map((voicePart) => (
            <TableMobileItem key={voicePart.id} url={route('voice-parts.edit', voicePart.id)}>
                <div className="flex-shrink-0">
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
                                    Singers: {voicePart.singers_count}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </TableMobileItem>
        ))}
    </TableMobile>
);

export default VoicePartTableMobile;