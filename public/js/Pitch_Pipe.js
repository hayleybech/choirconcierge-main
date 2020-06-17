class Pitch_Pipe {
    constructor(selector, key) {
        this.el = document.querySelector(selector);
        this.el_key = this.el.querySelector('.key');
        this.synth = new Tone.Synth().toMaster();

        this.DEFAULT_OCTAVE = 4;

        this.setKey(key);
        this.bindAll();
    }

    play() {
        this.synth.triggerAttack( this.pitch );
    }

    stop() {
        this.synth.triggerRelease();
    }

    bindAll() {
        let self = this;
        this.el.addEventListener('mousedown', function(){
            self.play();
        });
        this.el.addEventListener('mouseup', function(){
            self.stop();
        });
    }

    setKey(key) {
        this.key = key;
        this.el_key.textContent = key;
        this.pitch = this.key + this.DEFAULT_OCTAVE.toString();
    }
}