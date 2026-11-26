declare module 'react-signature-canvas' {
  import { Component } from 'react';
  export interface SignatureCanvasProps {
    canvasProps?: React.CanvasHTMLAttributes<HTMLCanvasElement>;
    clearOnResize?: boolean;
    backgroundColor?: string;
    penColor?: string;
    velocityFilterWeight?: number;
    minWidth?: number;
    maxWidth?: number;
    minDistance?: number;
    dotSize?: number | (() => number);
    throttle?: number;
    onBegin?: () => void;
    onEnd?: () => void;
  }
  export default class SignatureCanvas extends Component<SignatureCanvasProps> {
    clear(): void;
    isEmpty(): boolean;
    fromDataURL(base64URL: string): void;
    toDataURL(type?: string, encoderOptions?: number): string;
    getTrimmedCanvas(): HTMLCanvasElement;
    getCanvas(): HTMLCanvasElement;
  }
}
