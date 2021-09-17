import React from "react";

export const PlayerContext = React.createContext({
    player: {
        title: null,
        src: null,
        play: () => {},
    },
});