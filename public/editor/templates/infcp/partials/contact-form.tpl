
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="well1 well-sm1">

                <form class="validable"
                      id="feedback-form"
                      action="/editor/core/feedback/" method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">
                                    Name</label>
                                <input type="text"
                                       class="form-control"
                                       name="name"
                                       placeholder="Enter name"
                                       required="required" />
                            </div>
                            <div class="form-group">
                                <label for="email">
                                    Your Email Address</label>
                                <div class="input-group input-group-sm">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span>
                                </span>
                                    <input
                                           type="email" class="form-control" name="email"
                                           placeholder="Enter email"
                                           data-rule-email="true"
                                           required="required" /></div>
                            </div>
                            <div class="form-group">
                                <label for="subject">
                                    Subject</label>
                                <select name="subject" required="required">
                                    <option value="Product Support">Product Support</option>
                                    <option value="Feature request">Feature request</option>
                                    <option value="Bug report">Bug report</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">
                                    Message</label>
                                <textarea name="message" class="form-control"
                                          rows="9" cols="25" required="required"
                                          placeholder="Message"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <input type="submit"
                                   name="send" value="Send message"
                                   class="btn btn-primary pull-right" id="btnContactUs"
                                   data-disable-on-submit="true"
                                    />

                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-4">
            <form>
                <legend>&nbsp;</legend>
                <address>
                    <br/>
                    <a href="#">Сайт</a>
                    <br/>
                    Адрес компании<br/>
                </address>
                <address>
                    <strong>Contacts</strong><br>
                    <a href="mailto:#">почта@собака.ру</a>
                </address>
            </form>
        </div>
    </div>
</div>
