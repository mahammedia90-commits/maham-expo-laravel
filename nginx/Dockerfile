FROM nginx:alpine

# Copy both configs into the image
COPY proxy.conf /etc/nginx/templates/proxy-ssl.conf
COPY proxy-initial.conf /etc/nginx/templates/proxy-initial.conf

# Smart entrypoint
COPY entrypoint.sh /docker-entrypoint-custom.sh
RUN chmod +x /docker-entrypoint-custom.sh

ENTRYPOINT ["/docker-entrypoint-custom.sh"]
CMD ["nginx", "-g", "daemon off;"]
