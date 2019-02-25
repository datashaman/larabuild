FROM datashaman/larabuild:base

USER user

RUN yarn install
RUN yarn run dev

USER root
