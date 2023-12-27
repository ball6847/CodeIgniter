FROM dunglas/frankenphp:latest

RUN apt update && \
	apt install -y procps inotify-tools && \
	rm -rf /var/lib/apt/lists/*

COPY start.sh /usr/local/bin/start.sh

ENTRYPOINT ["/usr/local/bin/start.sh"]
