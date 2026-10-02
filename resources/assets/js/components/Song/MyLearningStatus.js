import React from 'react';
import LearningStatusDropdown from "./LearningStatusDropdown";
import SimplePanel from "../SimplePanel";
import useRoute from "../../hooks/useRoute";

const MyLearningStatus = ({ song }) => {
    const { route } = useRoute();
    const allowedStatuses = song.can?.update_song
        ? ['not-started', 'assessment-ready', 'performance-ready']
        : ['not-started', 'assessment-ready'];

    return (
        <SimplePanel>
            <LearningStatusDropdown
              status={song.my_learning.status}
              allowedStatuses={allowedStatuses}
              href={route('songs.my-learning.update', { song })}
              method="post"
            />
        </SimplePanel>
    );
}

export default MyLearningStatus;
