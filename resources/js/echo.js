import { ApolloLink, Observable } from "apollo-link"

class EchoLink extends ApolloLink {
    request(operation, forward) {
        return new Observable(observer => {
            // Check the result of the operation
            forward(operation).subscribe({
                next: data => {
                    // If the operation has the subscription extension, it's a subscription
                    const subscriptionChannel = this._getChannel(
                        data,
                        operation
                    )

                    if (subscriptionChannel) {
                        this._createSubscription(subscriptionChannel, observer)
                    } else {
                        // No subscription found in the response, pipe data through
                        observer.next(data)
                        observer.complete()
                    }
                }
            })
        })
    }

    _getChannel(data, operation) {
        return !!data.extensions &&
            !!data.extensions.lighthouse_subscriptions &&
            !!data.extensions.lighthouse_subscriptions.channels
            ? data.extensions.lighthouse_subscriptions.channels[
                  operation.operationName
              ]
            : null
    }

    _createSubscription(subscriptionChannel, observer) {
        console.log('_createSubscription', subscriptionChannel)

        Echo.private(subscriptionChannel)
            .listen("lighthouse-subscription", payload => {
                console.log('lighthouse-subscription', payload)

                if (!payload.more) {
                    // This is the end, the server says to unsubscribe
                    this.unsubscribe()
                    observer.complete()
                }
                const result = payload.result
                if (result) {
                    // Send the new response to listeners
                    observer.next(result)
                }
            })
    }
}

export default EchoLink
