import React, {useState} from 'react';
import PitchButton from "../../components/PitchButton";
import SongStatusTag from "../../components/SongStatusTag";
import TableMobile, {TableMobileLink} from "../../components/TableMobile";
import SongStatus from "../../SongStatus";
import {Synth} from "tone";
import useRoute from "../../hooks/useRoute";

const SongTableMobile = ({ songs }) => {
    const { route } = useRoute();

    const [synth] = useState(() => new Synth().toDestination());

    return (
        <TableMobile>
            {songs.map((song) => (
                <li key={song.id} className="flex pl-4">
                    <div className="shrink-0 py-3">
                        <PitchButton synth={synth} note={song.pitch.split('/')[0]} size="sm" />
                    </div>
                    <TableMobileLink url={route('songs.show', {song})}>
                        <div className="min-w-0 flex-1 lg:grid lg:grid-cols-2 lg:gap-4">
                            <div className="flex items-center justify-between">
                                <p className="flex items-center min-w-0 mr-1.5">
                                    <SongStatusTag status={new SongStatus(song.status.slug)} />
                                    <span className="text-sm font-medium text-purple-600 truncate">{song.title}</span>
                                </p>
                            </div>
                        </div>
                    </TableMobileLink>
                </li>
            ))}
        </TableMobile>
    );
}

export default SongTableMobile;