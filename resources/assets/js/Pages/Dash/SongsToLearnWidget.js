import React from 'react';
import SectionTitle from "../../components/SectionTitle";
import Panel from "../../components/Panel";
import TableMobile, {TableMobileItem} from "../../components/TableMobile";
import LearningStatusTag from "../../components/Song/LearningStatusTag";
import LearningStatus from "../../LearningStatus";
import useRoute from "../../hooks/useRoute";

const SongsToLearnWidget = ({ songs }) => {
    const { route } = useRoute();

    return (
        <Panel header={<SectionTitle>Songs to Learn</SectionTitle>} noPadding>
            {songs.length > 0 ? (
                <TableMobile>
                    {songs.map((song) => (
                        <TableMobileItem url={route('songs.show', {song})} key={song.id}>
                            <div className="text-sm font-medium text-purple-800 shrink-1">{song.title}</div>
                            <div className="text-sm">
                                <LearningStatusTag status={new LearningStatus(song.my_learning.status)} />
                            </div>
                        </TableMobileItem>
                    ))}
                </TableMobile>
            ) : (
                <p className="px-4 py-4 sm:px-6">No new songs to learn.</p>
            )}
        </Panel>
    );
}

export default SongsToLearnWidget;