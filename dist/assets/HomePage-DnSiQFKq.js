import{c as le,r as d,j as e,g as ce,u as O,L as N,M as Q,a as de,b as me,d as ue,e as pe,p as ge,S as xe,f as L}from"./main-D478NNWS.js";import{C as ee}from"./ContentCard-Bj6E3VJY.js";import{N as fe}from"./NeuralBackground-Bk-SxTDp.js";import{S as be}from"./SEO-D6R6bQbr.js";import{u as te,f as re}from"./useArticles-pbLeU0VJ.js";import{u as he,f as ye}from"./useTools-Cbzju1N0.js";import{u as we}from"./useCourses-BCFTrQ3-.js";/**
 * @license lucide-react v0.553.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const ve=[["path",{d:"M5 12h14",key:"1ays0h"}],["path",{d:"m12 5 7 7-7 7",key:"xquz4c"}]],K=le("arrow-right",ve),ne=["Create a discipleship training video that sparks movements in unreached communities...","Generate an engaging video teaching biblical principles for multiplying disciples...","Produce a testimony video showing how media catalyzes church planting movements...","Make an interactive video equipping believers to share the Gospel through digital tools...","Create a training series on facilitating discovery Bible studies in oral cultures...","Generate content showing how one faithful disciple can multiply into thousands..."];function je(){return typeof window>"u"?!1:window.matchMedia("(prefers-reduced-motion: reduce)").matches}function _e(){const g=d.useRef(null),c=d.useRef(null),r=d.useRef(null),f=d.useRef(null),i=d.useRef(null),a=d.useRef(null),p=d.useRef("desktop"),o=d.useRef(!0),u=d.useRef(0),R=d.useRef(0),v=d.useRef(!1),A=d.useRef([]),k=d.useRef(0),j=6,I=d.useRef();return d.useEffect(()=>{if(je())return;const _=(t="data",m=!1)=>{if(!r.current||!c.current||!i.current||!g.current)return;const b=document.createElement("div");b.className=`particle ${t}`;const n=c.current.getBoundingClientRect(),x=g.current.getBoundingClientRect(),s=n.left-x.left+Math.random()*n.width,w=n.top-x.top+Math.random()*n.height,C=t==="create"?x.height*.15:x.height*.5;let P;m||Math.random()<.6?P=x.width*.2+(Math.random()-.5)*200:P=x.width*.75+(Math.random()-.5)*100;const M={element:b,x:s,y:w,targetX:P,targetY:C,duration:2e3+Math.random()*1e3,startTime:performance.now(),size:3+Math.random()*4,opacity:0};b.style.width=`${M.size}px`,b.style.height=`${M.size}px`,b.style.left=`${M.x}px`,b.style.top=`${M.y}px`,b.style.opacity="0",r.current.appendChild(b),A.current.push(M)},T=(t,m)=>{const b=m-t.startTime,n=Math.min(b/t.duration,1),x=n<.5?2*n*n:1-Math.pow(-2*n+2,2)/2,s=(t.x+t.targetX)/2+(Math.random()-.5)*100;return t.x=t.x+(s-t.x)*x*.5,t.y=t.y+(t.targetY-t.y)*x,n<.2?t.opacity=n/.2:n>.8?t.opacity=(1-n)/.2:t.opacity=.8,t.element.style.left=`${t.x}px`,t.element.style.top=`${t.y}px`,t.element.style.opacity=t.opacity.toString(),n>.4&&n<.5&&Math.random()>.95&&l(),n>=1?(t.element.remove(),!1):!0},l=()=>{if(!f.current||!i.current)return;if(k.current>=j){const s=f.current.querySelectorAll(".video-frame");s.length>0&&s[0].remove()}const t=document.createElement("div");t.className="video-frame";const m=k.current*137.5*(Math.PI/180),b=120+k.current*15,n=Math.cos(m)*b,x=Math.sin(m)*b;t.style.transform=`translate(-50%, -50%) translate(${n}px, ${x}px) scale(0)`,t.style.opacity="0",f.current.appendChild(t),setTimeout(()=>{t.parentElement&&(t.style.transition="all 1.6s cubic-bezier(0.34, 1.56, 0.64, 1)",t.style.transform=`translate(-50%, -50%) translate(${n}px, ${x}px) scale(1)`,t.style.opacity="1")},50),setTimeout(()=>{if(!i.current||!g.current||!t.parentElement)return;const s=i.current.getBoundingClientRect(),w=g.current.getBoundingClientRect(),C=t.getBoundingClientRect(),P=s.left-w.left+s.width/2-(C.left-w.left)-C.width/2,M=s.top-w.top+s.height/2-(C.top-w.top)-C.height/2;t.style.transition="all 2.5s cubic-bezier(0.4, 0.0, 0.2, 1)",t.style.transform=`translate(-50%, -50%) translate(${P}px, ${M}px) scale(2.5)`,t.style.opacity="0",setTimeout(()=>{t.parentElement&&t.remove()},2500)},3500),k.current++},y=()=>{const t=8+Math.floor(Math.random()*5);for(let m=0;m<t;m++)setTimeout(()=>{l()},m*80);for(let m=0;m<25;m++)setTimeout(()=>{const b=m<15;_("data",b)},m*40)},z=()=>{const t=["desktop","tablet","mobile"],b=(t.indexOf(p.current)+1)%t.length;p.current=t[b],a.current&&a.current.setAttribute("data-platform",p.current)},h=()=>{a.current&&(a.current.style.animation="none",a.current.offsetWidth,a.current.style.transition="opacity 0.8s ease-out",a.current.style.opacity="0.2",setTimeout(()=>{a.current&&(a.current.style.opacity="1")},800))},$=()=>{if(v.current||!c.current)return;v.current=!0,z(),h(),y();const t=ne[u.current];R.current=0;const m=setInterval(()=>{c.current&&(R.current<t.length?(c.current.textContent=t.substring(0,R.current+1),R.current++,R.current%3===0&&_()):(clearInterval(m),v.current=!1,setTimeout(()=>{c.current&&(u.current=(u.current+1)%ne.length,c.current.style.opacity="0",setTimeout(()=>{c.current&&(c.current.textContent="",c.current.style.opacity="1",$())},1e3))},5e3)))},50)},q=t=>{if(!o.current){I.current=requestAnimationFrame(q);return}for(let m=A.current.length-1;m>=0;m--)T(A.current[m],t)||A.current.splice(m,1);I.current=requestAnimationFrame(q)};setTimeout(()=>{$(),I.current=requestAnimationFrame(q)},500),setTimeout(()=>{a.current&&(a.current.style.animation="none")},5e3);const H=setInterval(()=>{v.current&&_()},80),W=setInterval(()=>{v.current&&Math.random()>.7&&l()},1200);setTimeout(()=>{for(let t=0;t<20;t++)setTimeout(()=>_(),t*50)},1e3);let F=null;return g.current&&typeof IntersectionObserver<"u"&&(F=new IntersectionObserver(([t])=>{o.current=t.isIntersecting},{threshold:.1}),F.observe(g.current)),()=>{I.current&&cancelAnimationFrame(I.current),F&&F.disconnect(),clearInterval(H),clearInterval(W),A.current.forEach(t=>t.element.remove())}},[]),e.jsxs("div",{ref:g,className:"hidden md:block absolute inset-0 overflow-hidden pointer-events-none z-0",style:{opacity:.5},children:[e.jsx("style",{children:`
                .genmap-bg-container .text-prompt {
                    font-size: 18px;
                    color: #e2e8f0;
                    line-height: 1.6;
                    text-align: left;
                    padding: 20px 30px;
                    background: rgba(15, 23, 42, 0.7);
                    border: 1px solid rgba(59, 130, 246, 0.3);
                    border-radius: 12px;
                    box-shadow: 0 4px 20px rgba(59, 130, 246, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.1);
                    backdrop-filter: blur(10px);
                    min-height: 60px;
                    position: relative;
                    transition: opacity 0.3s;
                }

                .genmap-bg-container .particle {
                    position: absolute;
                    border-radius: 50%;
                    pointer-events: none;
                    will-change: transform, opacity;
                }

                .genmap-bg-container .particle.data {
                    background: #3b82f6;
                    box-shadow: 0 0 10px rgba(59, 130, 246, 0.8), 0 0 20px rgba(59, 130, 246, 0.4);
                }

                .genmap-bg-container .particle.process {
                    background: #8b5cf6;
                    box-shadow: 0 0 10px rgba(139, 92, 246, 0.8), 0 0 20px rgba(139, 92, 246, 0.4);
                }

                .genmap-bg-container .particle.create {
                    background: #06b6d4;
                    box-shadow: 0 0 10px rgba(6, 182, 212, 0.8), 0 0 20px rgba(6, 182, 212, 0.4);
                }

                .genmap-bg-container .video-frame {
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    width: 160px;
                    height: 90px;
                    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
                    border: 2px solid rgba(139, 92, 246, 0.5);
                    border-radius: 8px;
                    box-shadow: 0 4px 20px rgba(139, 92, 246, 0.3);
                    opacity: 0;
                    will-change: transform, opacity;
                }

                .genmap-bg-container .video-frame::before {
                    content: '';
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    width: 0;
                    height: 0;
                    border-style: solid;
                    border-width: 10px 0 10px 16px;
                    border-color: transparent transparent transparent rgba(139, 92, 246, 0.7);
                }

                .genmap-bg-container .youtube-layer {
                    opacity: 0;
                    animation: fadeInYoutube 2s ease-out 3s forwards;
                }

                @keyframes fadeInYoutube {
                    from {
                        opacity: 0;
                        transform: translateY(-20px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }

                .genmap-bg-container .youtube-player {
                    background: #282828;
                    border-radius: 12px;
                    overflow: hidden;
                    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.5);
                    transition: all 0.5s ease;
                    position: relative;
                }

                /* Platform-specific player styling */
                /* Desktop - Monitor screen */
                .genmap-bg-container .youtube-layer[data-platform="desktop"] .youtube-player {
                    background: #1a1a1a;
                    border-radius: 8px;
                    padding: 0;
                    box-shadow: 
                        0 8px 30px rgba(0, 0, 0, 0.5),
                        inset 0 0 0 1px rgba(255, 255, 255, 0.1);
                    border: 2px solid rgba(60, 60, 60, 0.5);
                    position: relative;
                }

                /* Desktop monitor stand/base */
                .genmap-bg-container .youtube-layer[data-platform="desktop"] .youtube-player::after {
                    content: '';
                    position: absolute;
                    bottom: -20px;
                    left: 50%;
                    transform: translateX(-50%);
                    width: 200px;
                    height: 12px;
                    background: linear-gradient(135deg, #2a2a2a 0%, #1a1a1a 100%);
                    border-radius: 4px;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
                }

                /* Tablet - iPad-like */
                .genmap-bg-container .youtube-layer[data-platform="tablet"] .youtube-player {
                    background: linear-gradient(135deg, #4a4a4a 0%, #3a3a3a 100%) !important;
                    border-radius: 24px !important;
                    padding: 12px !important;
                    box-shadow: 
                        0 12px 40px rgba(0, 0, 0, 0.9), 
                        inset 0 0 0 5px rgba(255, 255, 255, 0.25),
                        inset 0 0 0 10px rgba(0, 0, 0, 0.6),
                        0 0 0 3px rgba(60, 60, 60, 0.8);
                    border: 3px solid rgba(100, 100, 100, 0.3);
                    transform: scale(0.95);
                    transition: all 0.5s ease;
                }

                /* Mobile - Portrait phone - Position in middle right, below text box */
                .genmap-bg-container .youtube-layer[data-platform="mobile"] {
                    top: 50% !important;
                    bottom: auto !important;
                    right: 5% !important;
                    width: 90% !important;
                    max-width: 360px !important;
                    transform: translateY(-50%) !important;
                }

                .genmap-bg-container .youtube-layer[data-platform="mobile"] .youtube-player {
                    background: linear-gradient(135deg, #4a4a4a 0%, #3a3a3a 100%) !important;
                    border-radius: 40px !important;
                    padding: 16px 8px !important;
                    box-shadow: 
                        0 4px 15px rgba(0, 0, 0, 0.4), 
                        inset 0 0 0 6px rgba(255, 255, 255, 0.3),
                        inset 0 0 0 12px rgba(0, 0, 0, 0.7),
                        0 0 0 4px rgba(80, 80, 80, 0.9) !important;
                    border: 4px solid rgba(150, 150, 150, 0.4) !important;
                    transform: scale(0.85) !important;
                    max-width: 360px !important;
                    margin: 0 auto;
                    transition: all 0.5s ease;
                }

                /* Tablet Frame - iPad-like */
                .genmap-bg-container .mobile-phone-frame {
                    position: absolute;
                    top: -12px;
                    left: -12px;
                    right: -12px;
                    bottom: -12px;
                    pointer-events: none;
                    z-index: 15;
                    opacity: 0;
                    transition: opacity 0.5s ease;
                    border: 6px solid rgba(50, 50, 50, 0.95);
                    border-radius: 28px;
                    background: linear-gradient(135deg, rgba(60, 60, 60, 0.9) 0%, rgba(40, 40, 40, 0.9) 100%);
                    box-shadow: 
                        inset 0 0 0 2px rgba(255, 255, 255, 0.2),
                        0 0 0 2px rgba(0, 0, 0, 0.6),
                        0 8px 20px rgba(0, 0, 0, 0.8);
                }

                .genmap-bg-container .youtube-layer[data-platform="tablet"] .mobile-phone-frame {
                    opacity: 1;
                }

                /* Mobile Phone Frame - Portrait phone */
                .genmap-bg-container .youtube-layer[data-platform="mobile"] .mobile-phone-frame {
                    opacity: 1;
                    border: 8px solid rgba(50, 50, 50, 0.95);
                    border-radius: 44px;
                    top: -16px;
                    left: -8px;
                    right: -8px;
                    bottom: -16px;
                }

                /* Mobile phone notch */
                .genmap-bg-container .youtube-layer[data-platform="mobile"] .mobile-phone-frame::before {
                    content: '';
                    position: absolute;
                    top: -8px;
                    left: 50%;
                    transform: translateX(-50%);
                    width: 120px;
                    height: 24px;
                    background: linear-gradient(135deg, rgba(50, 50, 50, 0.98) 0%, rgba(40, 40, 40, 0.98) 100%);
                    border-radius: 0 0 18px 18px;
                    z-index: 16;
                    border: 2px solid rgba(0, 0, 0, 0.3);
                    border-top: none;
                }

                .genmap-bg-container .mobile-status-bar {
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    height: 24px;
                    background: rgba(0, 0, 0, 0.7);
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 0 12px;
                    font-size: 10px;
                    color: rgba(255, 255, 255, 0.95);
                    font-weight: 600;
                    z-index: 11;
                    backdrop-filter: blur(10px);
                    opacity: 0;
                    transition: opacity 0.5s ease;
                }

                .genmap-bg-container .youtube-layer[data-platform="tablet"] .mobile-status-bar,
                .genmap-bg-container .youtube-layer[data-platform="mobile"] .mobile-status-bar {
                    opacity: 1;
                }

                .genmap-bg-container .mobile-time {
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
                    letter-spacing: 0.5px;
                }

                .genmap-bg-container .mobile-status-icons {
                    display: flex;
                    gap: 4px;
                    align-items: center;
                    font-size: 9px;
                }

                .genmap-bg-container .player-screen {
                    position: relative;
                    width: 100%;
                    padding-top: 56.25%;
                    background: #000;
                    overflow: hidden;
                }

                /* Mobile portrait aspect ratio */
                .genmap-bg-container .youtube-layer[data-platform="mobile"] .player-screen {
                    padding-top: 177.78%; /* 9:16 aspect ratio for portrait phone */
                }

                .genmap-bg-container .player-screen::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: linear-gradient(135deg, #1e1e1e 0%, #0a0a0a 100%);
                    opacity: 0.8;
                    transition: background 0.5s ease;
                }

                /* Platform-specific screen backgrounds */
                .genmap-bg-container .youtube-layer[data-platform="desktop"] .player-screen::before {
                    background: linear-gradient(135deg, #1e1e1e 0%, #0a0a0a 100%);
                }

                .genmap-bg-container .youtube-layer[data-platform="tablet"] .player-screen {
                    border-radius: 16px;
                    overflow: hidden;
                }

                .genmap-bg-container .youtube-layer[data-platform="tablet"] .player-screen::before {
                    background: linear-gradient(135deg, #0a0a0a 0%, #000000 100%);
                    border-radius: 16px;
                }

                .genmap-bg-container .youtube-layer[data-platform="mobile"] .player-screen {
                    border-radius: 24px;
                    overflow: hidden;
                    margin-top: 0;
                }

                .genmap-bg-container .youtube-layer[data-platform="mobile"] .player-screen::before {
                    background: linear-gradient(135deg, #0a0a0a 0%, #000000 100%);
                    border-radius: 24px;
                }

                .genmap-bg-container .gear-image-container {
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    z-index: 100;
                    pointer-events: none;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                .genmap-bg-container .gear-image {
                    width: 160px;
                    height: 160px;
                    animation: rotateGear 3s linear infinite;
                    opacity: 0.9;
                    position: relative;
                    z-index: 101;
                }

                @keyframes rotateGear {
                    from {
                        transform: rotate(0deg);
                    }
                    to {
                        transform: rotate(360deg);
                    }
                }


                .genmap-bg-container .player-controls {
                    background: #181818;
                    padding: 8px 12px;
                    transition: background 0.5s ease;
                }

                .genmap-bg-container .youtube-layer[data-platform="desktop"] .player-controls {
                    background: #181818;
                }

                .genmap-bg-container .youtube-layer[data-platform="tablet"] .player-controls {
                    background: #0a0a0a;
                    border-radius: 0 0 16px 16px;
                }

                .genmap-bg-container .youtube-layer[data-platform="mobile"] .player-controls {
                    background: #0a0a0a;
                    border-radius: 0 0 24px 24px;
                    margin-top: -1px;
                }

                .genmap-bg-container .progress-bar {
                    width: 100%;
                    height: 3px;
                    background: rgba(255, 255, 255, 0.2);
                    border-radius: 2px;
                    margin-bottom: 8px;
                    position: relative;
                    overflow: hidden;
                }

                .genmap-bg-container .progress-fill {
                    height: 100%;
                    background: #ff0000;
                    width: 0%;
                    border-radius: 2px;
                    animation: progressFill 8s linear infinite;
                    transition: background 0.5s ease;
                }

                /* Platform-specific progress colors */
                .genmap-bg-container .youtube-layer[data-platform="desktop"] .progress-fill {
                    background: #ff0000;
                }

                .genmap-bg-container .youtube-layer[data-platform="tablet"] .progress-fill {
                    background: #007aff;
                }

                .genmap-bg-container .youtube-layer[data-platform="mobile"] .progress-fill {
                    background: #007aff;
                }

                @keyframes progressFill {
                    0% { width: 0%; }
                    100% { width: 100%; }
                }

                .genmap-bg-container .control-buttons {
                    display: flex;
                    align-items: center;
                    gap: 12px;
                    color: #fff;
                    font-size: 12px;
                }

                .genmap-bg-container .control-icon {
                    width: 24px;
                    height: 24px;
                    background: rgba(255, 255, 255, 0.1);
                    border-radius: 4px;
                }

                .genmap-bg-container .time-display {
                    color: rgba(255, 255, 255, 0.7);
                    font-size: 12px;
                }

                .genmap-bg-container .right-controls {
                    display: flex;
                    gap: 8px;
                    margin-left: auto;
                }

                @media (max-width: 768px) {
                    .genmap-bg-container .text-prompt {
                        font-size: 14px;
                        padding: 15px 20px;
                    }
                    
                    .genmap-bg-container .video-frame {
                        width: 120px;
                        height: 68px;
                    }

                    .genmap-bg-container .gear-image {
                        width: 120px;
                        height: 120px;
                    }
                }

                @media (max-width: 480px) {
                    .genmap-bg-container .text-prompt {
                        font-size: 11px;
                        padding: 10px 14px;
                        min-height: 50px;
                    }
                    
                    .genmap-bg-container .video-frame {
                        width: 90px;
                        height: 51px;
                    }
                    
                    .genmap-bg-container .control-icon {
                        width: 20px;
                        height: 20px;
                    }

                    .genmap-bg-container .gear-image {
                        width: 90px;
                        height: 90px;
                    }
                }
            `}),e.jsxs("div",{className:"genmap-bg-container w-full h-full",children:[e.jsx("div",{className:"absolute",style:{bottom:"calc(19% - 15px)",right:"5%",width:"80%",maxWidth:"700px",zIndex:10},children:e.jsx("div",{ref:c,className:"text-prompt"})}),e.jsx("div",{ref:r,className:"absolute inset-0",style:{zIndex:5}}),e.jsx("div",{className:"absolute",style:{top:"48%",right:"5%",transform:"translateY(-50%)",width:"600px",height:"340px",zIndex:7},children:e.jsx("div",{ref:f,className:"relative w-full h-full"})}),e.jsx("div",{ref:a,className:"youtube-layer absolute","data-platform":"desktop",style:{top:"8%",right:"5%",width:"90%",maxWidth:"640px",zIndex:10},children:e.jsxs("div",{className:"youtube-player",children:[e.jsx("div",{className:"mobile-phone-frame"}),e.jsxs("div",{ref:i,className:"player-screen",children:[e.jsxs("div",{className:"mobile-status-bar",children:[e.jsx("span",{className:"mobile-time",children:"9:41"}),e.jsxs("div",{className:"mobile-status-icons",children:[e.jsx("span",{className:"mobile-signal",children:"📶"}),e.jsx("span",{className:"mobile-wifi",children:"📶"}),e.jsx("span",{className:"mobile-battery",children:"🔋"})]})]}),e.jsx("div",{className:"gear-image-container",children:e.jsx("img",{src:ce("gear.png"),alt:"Spinning gear",className:"gear-image"})})]}),e.jsxs("div",{className:"player-controls",children:[e.jsx("div",{className:"progress-bar",children:e.jsx("div",{className:"progress-fill"})}),e.jsxs("div",{className:"control-buttons",children:[e.jsx("div",{className:"control-icon"}),e.jsx("div",{className:"time-display",children:"0:00 / 2:45"}),e.jsxs("div",{className:"right-controls",children:[e.jsx("div",{className:"control-icon"}),e.jsx("div",{className:"control-icon"}),e.jsx("div",{className:"control-icon"})]})]})]})]})})]})]})}function Ne({title:g,subtitle:c,description:r,ctaText:f,ctaLink:i="/articles"}){const{t:a}=O(),p=f||a("hero_cta_explore_resources");return e.jsxs("section",{className:"relative overflow-hidden bg-gradient-to-br from-secondary-900 via-secondary-800 to-secondary-700 text-white",children:[e.jsx(_e,{}),e.jsx("div",{className:"container-custom py-24 md:py-40 relative z-10",children:e.jsxs("div",{className:"max-w-4xl",children:[e.jsx("p",{className:"text-accent-500 font-semibold mb-4 text-lg",children:c}),e.jsx("h1",{className:"text-4xl md:text-6xl font-bold mb-6 leading-tight",children:g}),r&&e.jsx("p",{className:"text-xl text-secondary-100 mb-8 leading-relaxed max-w-3xl",children:r}),e.jsxs("div",{className:"flex flex-wrap gap-4",children:[e.jsx(N,{to:i,className:"inline-flex items-center justify-center px-8 py-4 border-2 border-white text-base font-semibold rounded-lg text-white bg-transparent hover:bg-white hover:text-secondary-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all duration-200",children:p}),e.jsx("a",{href:"https://ai.kingdom.training/about/",className:"inline-flex items-center justify-center px-8 py-4 border-2 border-white/30 text-base font-semibold rounded-lg text-white hover:border-white hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all duration-200",children:a("hero_cta_about_us")})]})]})}),e.jsx("div",{className:"relative h-0 bg-white",children:e.jsx("svg",{className:"absolute top-0 w-full h-16 -mt-16",preserveAspectRatio:"none",viewBox:"0 0 1440 54",fill:"none",children:e.jsx("path",{fill:"currentColor",className:"text-white",d:"M0 32L120 37.3C240 43 480 53 720 53.3C960 53 1200 43 1320 37.3L1440 32V54H1320C1200 54 960 54 720 54C480 54 240 54 120 54H0V32Z"})})})]})}function ke({variant:g="inline",title:c,description:r,showEmailInput:f=!1,className:i="",whiteBackground:a=!1,noWrapper:p=!1}){const{t:o}=O(),[u,R]=d.useState(""),[v,A]=d.useState(!0),k=d.useRef(null);d.useEffect(()=>{async function _(){try{const T=await de('[go_display_opt_in source="kt_news" name="Kingdom.Training"]');R(T)}catch(T){console.error("Error fetching shortcode:",T)}finally{A(!1)}}_()},[]),d.useEffect(()=>{var m,b;if(!u||v||!f)return;const _=(m=k.current)==null?void 0:m.querySelector("form"),T=(b=k.current)==null?void 0:b.querySelector("#go-submit-form-button");if(!_||!T)return;const l=_,y=T;window.cf_token=null;const z="0x4AAAAAAA1dT7LSth0AgFDm",h=()=>new Promise(n=>{if(typeof window.turnstile<"u"){console.log("Turnstile API already loaded"),n();return}document.querySelectorAll('script[src*="challenges.cloudflare.com/turnstile"]').forEach(w=>{typeof window.turnstile>"u"&&w.remove()});const s=document.createElement("script");s.src="https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit",s.async=!0,s.onload=()=>{console.log("Turnstile script loaded successfully"),n()},s.onerror=w=>{console.error("Failed to load Turnstile script:",w),n()},document.head.appendChild(s)}),$=async()=>{if(console.log("Initializing Turnstile widget..."),await h(),typeof window.turnstile>"u"){console.error("Turnstile API not available after script load");return}let n=l.querySelector(".cf-turnstile");if(!n){console.log("Creating Turnstile widget container"),n=document.createElement("div"),n.className="cf-turnstile",n.id="turnstile-widget";const s=l.querySelector("#go-submit-form-button");s&&s.parentNode?s.parentNode.insertBefore(n,s):l.appendChild(n)}if(n.setAttribute("data-sitekey",z),n.setAttribute("data-theme","light"),n.querySelector("iframe")){console.log("Turnstile widget already rendered");return}n.innerHTML="";try{console.log("Rendering Turnstile widget with site key:",z);const s=window.turnstile.render(n,{sitekey:z,theme:"light",callback:w=>{console.log("Turnstile token received"),window.cf_token=w,typeof window.save_cf=="function"&&window.save_cf(w)},"error-callback":()=>{console.error("Turnstile widget error")},"expired-callback":()=>{console.log("Turnstile token expired"),window.cf_token=null}});console.log("Turnstile widget rendered, ID:",s)}catch(s){console.error("Error rendering Turnstile widget:",s)}},q=()=>{const n=l.querySelector(".cf-turnstile");if(!(n==null?void 0:n.getAttribute("data-sitekey"))){console.warn("Cloudflare Turnstile site key not configured");return}return typeof window.turnstile>"u"?(console.log("Waiting for Cloudflare Turnstile script to load..."),!1):(n==null?void 0:n.querySelector("iframe"))?!0:(console.log("Waiting for Cloudflare Turnstile widget to render..."),!1)};window.save_cf=function(n){console.log("save_cf called with token"),window.cf_token=n};const H=setTimeout(async()=>{await $(),setTimeout(()=>{q()||(console.warn("Turnstile widget may not have rendered properly. Retrying..."),$())},2e3)},200),W=()=>{var x;const n=(x=k.current)==null?void 0:x.querySelectorAll("script");if(!n)return null;for(const s of Array.from(n)){const C=(s.textContent||"").match(/X-WP-Nonce['"]:\s*['"]([^'"]+)['"]/);if(C)return C[1]}return null},F=()=>window.cf_token||null,t=async n=>{var x,s,w,C,P;n.preventDefault(),n.stopPropagation();try{const M=((x=l.querySelector('input[name="email2"]'))==null?void 0:x.value)||"",B=((s=l.querySelector('input[name="email"]'))==null?void 0:s.value)||"",ae=((w=l.querySelector('input[name="first_name"]'))==null?void 0:w.value)||"",oe=((C=l.querySelector('input[name="last_name"]'))==null?void 0:C.value)||"",se=(P=l.querySelector("#confirm-subscribe"))==null?void 0:P.checked;if(B)return;if(!se){const S=l.querySelector(".dt-form-error");S&&(S.textContent=o("newsletter_confirm_subscribe"),S.style.display="block");return}const X=W(),G=F();if(!X)throw console.error("WordPress nonce not found"),new Error(o("error_security_token_not_found"));const E=l.querySelector(".cf-turnstile"),U=E==null?void 0:E.getAttribute("data-sitekey");if(U&&!G){const S=l.querySelector(".dt-form-error");S&&(E&&E.offsetHeight>0&&E.offsetWidth>0?S.textContent=o("newsletter_security_complete"):S.textContent=o("newsletter_security_loading"),S.style.display="block",E&&E.scrollIntoView({behavior:"smooth",block:"center"}));return}U||console.warn("Cloudflare Turnstile site key not configured. Form submission may fail on the server.");const V=l.getAttribute("action")||"/wp-json/go-webform/double-optin",ie=V.startsWith("http")?V:`${window.location.origin}${V}`;y.setAttribute("disabled","disabled");const J=y.textContent;y.textContent=o("ui_submitting");const Y=l.querySelector(".dt-form-error");Y&&(Y.style.display="none");const Z=await fetch(ie,{method:"POST",headers:{"Content-Type":"application/json","X-WP-Nonce":X},body:JSON.stringify({email:M,first_name:ae,last_name:oe,source:"kt_news",cf_turnstile:G})}),D=await Z.json();if(Z.ok&&D!==!1){y.textContent=o("newsletter_subscribed"),y.classList.add("bg-green-500"),l.reset();const S=l.querySelector(".dt-form-success");S&&(S.textContent=o("newsletter_check_email"),S.style.display="block"),setTimeout(()=>{y.removeAttribute("disabled"),y.textContent=J,y.classList.remove("bg-green-500")},3e3)}else y.removeAttribute("disabled"),y.textContent=J||o("newsletter_try_again"),Y&&(Y.textContent=(D==null?void 0:D.message)||o("error_subscribe_failed"),Y.style.display="block")}catch(M){console.error("Error submitting form:",M),y.removeAttribute("disabled"),y.textContent=o("newsletter_try_again");const B=l.querySelector(".dt-form-error");B&&(B.textContent=o("error_subscribe_failed"),B.style.display="block")}};return l.addEventListener("submit",t),()=>{clearTimeout(H),l.removeEventListener("submit",t)}},[u,v,f]);const j=c||o("newsletter_stay_connected"),I=r||o("newsletter_default_description");if(g==="banner"){const _=a?"bg-white text-gray-900":"bg-gradient-to-r from-primary-800 to-primary-600 text-white",T=a?"text-gray-900":"",l=a?"text-gray-700":"text-primary-100",y=a?"text-primary-600":"text-accent-500",z=e.jsx("div",{className:`${p?"":_} ${p?"":"py-12"} ${i}`,children:e.jsx("div",{className:"container-custom",children:e.jsxs("div",{className:"max-w-4xl mx-auto text-center",children:[e.jsx(Q,{className:`w-12 h-12 mx-auto mb-4 ${y}`}),e.jsx("h2",{className:`text-3xl font-bold mb-4 ${T}`,children:j}),e.jsx("p",{className:`text-xl mb-8 max-w-2xl mx-auto ${l}`,children:I}),f?e.jsx("div",{className:"max-w-md mx-auto",ref:k,children:v?e.jsx("div",{className:"flex items-center justify-center py-4",children:e.jsx("div",{className:"w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"})}):u?e.jsx("div",{dangerouslySetInnerHTML:{__html:u}}):null}):e.jsxs(N,{to:"/newsletter",className:`inline-flex items-center justify-center px-8 py-4 font-semibold rounded-lg transition-colors duration-200 text-lg ${a?"bg-primary-600 hover:bg-primary-700 text-white":"bg-accent-600 hover:bg-accent-500 text-secondary-900"}`,children:[o("nav_subscribe_newsletter"),e.jsx(K,{className:"w-5 h-5 ml-2"})]})]})})});return p?z:e.jsx("section",{children:z})}return g==="card"?e.jsx("div",{className:`bg-background-50 border-2 border-primary-200 rounded-lg p-6 md:p-8 ${i}`,children:e.jsxs("div",{className:"flex items-start gap-4",children:[e.jsx("div",{className:"flex-shrink-0 w-12 h-12 bg-primary-500 rounded-full flex items-center justify-center",children:e.jsx(Q,{className:"w-6 h-6 text-white"})}),e.jsxs("div",{className:"flex-1",children:[e.jsx("h3",{className:"text-xl font-bold text-gray-900 mb-2",children:j}),e.jsx("p",{className:"text-gray-700 mb-4",children:I}),f?e.jsx("div",{ref:k,children:v?e.jsx("div",{className:"flex items-center justify-center py-4",children:e.jsx("div",{className:"w-5 h-5 border-2 border-primary-500 border-t-transparent rounded-full animate-spin"})}):u?e.jsx("div",{dangerouslySetInnerHTML:{__html:u}}):null}):e.jsxs(N,{to:"/newsletter",className:"inline-flex items-center text-primary-500 hover:text-primary-600 font-semibold",children:[o("nav_subscribe_now"),e.jsx(K,{className:"w-4 h-4 ml-1"})]})]})]})}):e.jsxs("div",{className:`flex flex-col sm:flex-row items-center justify-between gap-4 p-6 bg-primary-50 rounded-lg ${i}`,children:[e.jsxs("div",{className:"flex-1",children:[e.jsx("h3",{className:"text-lg font-semibold text-gray-900 mb-1",children:j}),e.jsx("p",{className:"text-sm text-gray-700",children:I})]}),f?e.jsx("div",{className:"w-full sm:w-auto",ref:k,children:v?e.jsx("div",{className:"flex items-center justify-center py-2",children:e.jsx("div",{className:"w-5 h-5 border-2 border-primary-500 border-t-transparent rounded-full animate-spin"})}):u?e.jsx("div",{dangerouslySetInnerHTML:{__html:u}}):null}):e.jsxs(N,{to:"/newsletter",className:"inline-flex items-center px-6 py-2 bg-primary-500 hover:bg-primary-600 text-white font-semibold rounded-lg transition-colors whitespace-nowrap",children:[o("newsletter_subscribe"),e.jsx(K,{className:"w-4 h-4 ml-2"})]})]})}function Te({title:g="Key Information",items:c,className:r=""}){return c.length===0?null:e.jsx("section",{className:`py-12 bg-gray-50 border-t border-gray-200 ${r}`,children:e.jsx("div",{className:"container-custom",children:e.jsxs("div",{className:"max-w-4xl mx-auto",children:[e.jsx("h2",{className:"text-2xl font-bold text-gray-900 mb-6",children:g}),e.jsx("dl",{className:"space-y-4",children:c.map((f,i)=>e.jsxs("div",{className:"bg-white p-4 rounded-lg shadow-sm",children:[e.jsx("dt",{className:"text-lg font-semibold text-gray-900 mb-2",children:f.term}),e.jsx("dd",{className:"text-gray-700 leading-relaxed",children:f.definition})]},i))})]})})})}function Ce({articles:g}){const c=d.useRef(null),r=d.useRef(null),f=d.useRef(null);if(d.useEffect(()=>{c.current&&(f.current=c.current.closest("section"));const o=()=>{if(!c.current&&!r.current||!f.current)return;const v=window.scrollY*.15;c.current&&(c.current.style.transform=`translateY(${v}px)`),r.current&&(r.current.style.transform=`translateY(${v}px)`)};return window.addEventListener("scroll",o,{passive:!0}),o(),()=>{window.removeEventListener("scroll",o)}},[]),!g||g.length===0)return null;const i=o=>{const u=document.createElement("DIV");return u.innerHTML=o,u.textContent||u.innerText||""},a=o=>{const u=i(o);return u.length>36?u.substring(0,36).trim()+"...":u},p={fontSize:"clamp(1.4rem, 2vw, 1.8rem)",lineHeight:"1.2",color:"rgba(107, 114, 128, 0.15)",fontWeight:500,fontFamily:"'Courier New', Courier, 'Lucida Console', Monaco, monospace",maxHeight:"2.4em",overflow:"hidden",overflowWrap:"break-word",filter:"blur(1px)",textShadow:"0 0 3px rgba(107, 114, 128, 0.15)"};return e.jsxs(e.Fragment,{children:[e.jsxs("div",{ref:c,className:"absolute left-0 w-1/2 pointer-events-none overflow-hidden",style:{top:"-15%",bottom:"-15%",height:"130%",willChange:"transform",zIndex:0},children:[e.jsx("div",{className:"absolute inset-0 pointer-events-none",style:{background:"linear-gradient(to left, white 0%, rgba(255, 255, 255, 0.8) 20%, rgba(255, 255, 255, 0.4) 40%, transparent 60%)",zIndex:1}}),e.jsx("div",{className:"h-full flex flex-col justify-start items-start pl-4 md:pl-8 lg:pl-16 pt-12 relative",style:{zIndex:0},children:g.map(o=>e.jsx("div",{className:"text-left mb-1 md:mb-1.5 max-w-[85%]",style:p,children:a(o.title.rendered)},`left-${o.id}`))})]}),e.jsxs("div",{ref:r,className:"absolute right-0 w-1/2 pointer-events-none overflow-hidden",style:{top:"-15%",bottom:"-15%",height:"130%",willChange:"transform",zIndex:0},children:[e.jsx("div",{className:"absolute inset-0 pointer-events-none",style:{background:"linear-gradient(to right, white 0%, rgba(255, 255, 255, 0.8) 20%, rgba(255, 255, 255, 0.4) 40%, transparent 60%)",zIndex:1}}),e.jsx("div",{className:"h-full flex flex-col justify-start items-end pr-4 md:pr-8 lg:pr-16 pt-12 relative",style:{zIndex:0},children:g.map(o=>e.jsx("div",{className:"text-right mb-1 md:mb-1.5 max-w-[85%]",style:p,children:a(o.title.rendered)},`right-${o.id}`))})]})]})}function Pe(){const{lang:g}=me(),c=ue(),{t:r,tWithReplace:f}=O(),i=pe(),a=g||ge(c.pathname).lang||void 0,p=a||i||null,{data:o=[],isLoading:u}=te({per_page:3,orderby:"date",order:"desc",lang:p||void 0}),{data:R=[],isLoading:v}=te({per_page:15,orderby:"date",order:"desc",lang:p||void 0}),{data:A=[],isLoading:k}=he({per_page:3,orderby:"date",order:"desc",lang:p||void 0}),{data:j=[],isLoading:I}=we(a,i),_=d.useMemo(()=>re(o,p),[o,p]),T=d.useMemo(()=>re(R,p),[R,p]),l=d.useMemo(()=>ye(A,p),[A,p]);if(u||v||k||I)return e.jsx("div",{className:"flex items-center justify-center min-h-[400px]",children:e.jsxs("div",{className:"text-center",children:[e.jsx("div",{className:"inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-primary-500"}),e.jsx("p",{className:"mt-4 text-gray-600",children:r("ui_loading")})]})});const z=typeof window<"u"?window.location.origin:"https://ai.kingdom.training";return e.jsxs(e.Fragment,{children:[e.jsx(be,{title:r("page_home"),description:r("seo_home_description"),keywords:"disciple making movements, media to movements, M2DMM, digital discipleship, online evangelism, church planting, unreached peoples, kingdom training, strategy course, MVP course"}),e.jsx(xe,{website:{name:"Kingdom.Training",url:z,description:r("footer_mission_statement")}}),e.jsx(Ne,{subtitle:r("hero_subtitle_media_ai"),title:r("hero_title_innovate"),description:r("hero_description"),ctaText:r("nav_start_mvp"),ctaLink:L("/strategy-courses",a||null,i)}),e.jsxs("section",{className:"relative py-12 bg-white overflow-hidden",children:[e.jsx(Ce,{articles:T}),e.jsx("div",{className:"relative z-10",children:e.jsx(ke,{variant:"banner",title:r("hero_newsletter_title"),description:r("home_newsletter_description"),showEmailInput:!1,className:"my-0",whiteBackground:!0,noWrapper:!0})})]}),e.jsxs("section",{className:"relative py-20 bg-gradient-to-br from-secondary-900 to-secondary-700 text-white overflow-hidden",children:[e.jsx(fe,{}),e.jsx("div",{className:"container-custom relative z-10",children:e.jsxs("div",{className:"max-w-4xl mx-auto text-center",children:[e.jsx("h2",{className:"text-3xl md:text-5xl font-bold mb-4",children:r("page_mvp_strategy_course")}),e.jsx("p",{className:"text-xl text-secondary-100 mb-8 max-w-2xl mx-auto",children:r("home_mvp_description")}),e.jsxs("div",{className:"bg-white/10 backdrop-blur-sm rounded-lg p-8 mb-8 text-left",children:[e.jsx("h3",{className:"text-xl font-semibold mb-4 text-accent-500",children:f("page_step_curriculum",{count:j.length>0?j.length:10})}),j.length>0?e.jsxs("div",{className:"grid md:grid-cols-2 gap-4 text-sm",children:[e.jsx("div",{className:"flex flex-col gap-4",children:j.slice(0,Math.ceil(j.length/2)).map((h,$)=>e.jsxs(N,{to:L(`/strategy-courses/${h.slug}`,a||null,i),className:"hover:text-accent-400 transition-colors",children:[h.steps||$+1,". ",h.title.rendered]},h.id))}),e.jsx("div",{className:"flex flex-col gap-4",children:j.slice(Math.ceil(j.length/2)).map((h,$)=>{const q=h.steps||Math.ceil(j.length/2)+$+1;return e.jsxs(N,{to:L(`/strategy-courses/${h.slug}`,a||null,i),className:"hover:text-accent-400 transition-colors",children:[q,". ",h.title.rendered]},h.id)})})]}):e.jsx("p",{className:"text-secondary-200",children:r("home_loading_steps")})]}),e.jsx(N,{to:L("/strategy-courses",a||null,i),className:"inline-flex items-center justify-center px-8 py-4 bg-accent-600 hover:bg-accent-500 text-secondary-900 font-semibold rounded-lg transition-colors duration-200 text-lg",children:r("nav_enroll_mvp")})]})})]}),e.jsx("section",{className:"py-16 bg-background-50",children:e.jsxs("div",{className:"container-custom",children:[e.jsxs("div",{className:"flex items-center justify-between mb-8",children:[e.jsx("h2",{className:"text-3xl font-bold text-gray-800",children:r("page_latest_articles")}),e.jsxs(N,{to:L("/articles",a||null,i),className:"text-primary-500 hover:text-primary-600 font-medium",children:[r("ui_view_all")," →"]})]}),_.length>0?e.jsx("div",{className:"grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8",children:_.map(h=>e.jsx(ee,{post:h,type:"articles",lang:a||null,defaultLang:i},h.id))}):e.jsxs("div",{className:"text-center py-12 bg-white rounded-lg",children:[e.jsx("p",{className:"text-gray-600 mb-4",children:r("msg_no_articles")}),e.jsxs(N,{to:L("/articles",a||null,i),className:"text-primary-500 hover:text-primary-600 font-medium",children:[r("ui_browse_all")," ",r("nav_articles").toLowerCase()," →"]})]})]})}),e.jsx("section",{className:"py-16 bg-white",children:e.jsxs("div",{className:"container-custom",children:[e.jsxs("div",{className:"flex items-center justify-between mb-8",children:[e.jsx("h2",{className:"text-3xl font-bold text-gray-800",children:r("page_featured_tools")}),e.jsxs(N,{to:L("/tools",a||null,i),className:"text-primary-500 hover:text-primary-600 font-medium",children:[r("ui_view_all")," →"]})]}),l.length>0?e.jsx("div",{className:"grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8",children:l.map(h=>e.jsx(ee,{post:h,type:"tools",lang:a||null,defaultLang:i},h.id))}):e.jsxs("div",{className:"text-center py-12 bg-background-50 rounded-lg",children:[e.jsx("p",{className:"text-gray-600 mb-4",children:r("msg_no_tools")}),e.jsxs(N,{to:L("/tools",a||null,i),className:"text-primary-500 hover:text-primary-600 font-medium",children:[r("ui_browse_all")," ",r("nav_tools").toLowerCase()," →"]})]})]})}),e.jsx("section",{className:"py-20 bg-primary-800 text-white",children:e.jsx("div",{className:"container-custom",children:e.jsxs("div",{className:"max-w-4xl mx-auto text-center",children:[e.jsxs("div",{className:"mb-12",children:[e.jsx("h2",{className:"text-3xl md:text-4xl font-bold text-white text-center mb-8",children:r("content_digital_disciple_making")}),e.jsx("div",{className:"relative w-full",style:{paddingBottom:"56.25%"},children:e.jsx("iframe",{src:"https://player.vimeo.com/video/436776178?title=0&byline=0&portrait=0",className:"absolute top-0 left-0 w-full h-full rounded-lg shadow-2xl",frameBorder:"0",allow:"autoplay; fullscreen; picture-in-picture",allowFullScreen:!0,title:r("video_kingdom_training_title")})})]}),e.jsx("h2",{className:"text-3xl md:text-4xl font-bold mb-6",children:r("content_heavenly_economy")}),e.jsx("p",{className:"text-lg text-primary-100 leading-relaxed mb-6",children:r("home_heavenly_economy")}),e.jsx("p",{className:"text-lg text-primary-100 leading-relaxed mb-8",children:r("home_mission_statement")}),e.jsxs("div",{className:"flex flex-wrap justify-center gap-4",children:[e.jsx(N,{to:L("/strategy-courses",a||null,i),className:"inline-flex items-center justify-center px-8 py-4 bg-accent-600 hover:bg-accent-500 text-secondary-900 font-semibold rounded-lg transition-colors duration-200",children:r("page_start_strategy_course")}),e.jsx(N,{to:L("/articles",a||null,i),className:"inline-flex items-center justify-center px-8 py-4 border-2 border-white/30 hover:border-white text-white font-semibold rounded-lg transition-colors duration-200",children:r("ui_read_articles")}),e.jsx(N,{to:L("/tools",a||null,i),className:"inline-flex items-center justify-center px-8 py-4 border-2 border-white/30 hover:border-white text-white font-semibold rounded-lg transition-colors duration-200",children:r("ui_explore_tools")})]})]})})}),e.jsx(Te,{title:r("content_key_information_m2dmm"),items:[{term:r("content_m2dmm_term"),definition:r("content_m2dmm_definition")},{term:r("content_digital_disciple_making_term"),definition:r("content_digital_disciple_making_definition")},{term:r("content_mvp_course_term"),definition:r("content_mvp_course_definition")},{term:r("content_ai_discipleship_term"),definition:r("content_ai_discipleship_definition")},{term:r("content_heavenly_economy_term"),definition:r("content_heavenly_economy_definition")},{term:r("content_kingdom_training_for_term"),definition:r("content_kingdom_training_for_definition")}]})]})}export{Pe as default};
