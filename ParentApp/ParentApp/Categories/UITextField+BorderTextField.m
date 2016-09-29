//
//  UITextField+BorderTextField.m
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 15/07/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "UITextField+BorderTextField.h"
#import "AppDelegate.h"


@implementation UITextField (BorderTextField)
#pragma clang diagnostic push
#pragma clang diagnostic ignored "-Wobjc-protocol-method-implementation"

- (void) addBorderline {
    
    //To make the border look very close to a UITextField
    // Custom Implementation
    if ([[self superview] superview]&&!([[[self superview] superview] isMemberOfClass:NSClassFromString(@"_UIAlertControllerTextFieldView")] || [self  isMemberOfClass:NSClassFromString(@"UIAlertSheetTextField")] || [self  isMemberOfClass:NSClassFromString(@"_UIAlertControllerTextField")])) {
        self.borderStyle = UITextBorderStyleNone;
        self.layer.borderWidth = 0.5;
        if (!self.secureTextEntry) {
            self.textColor = kColorGrayDark;
        }
        self.layer.borderColor = [UIColor grayColor].CGColor;
        self.backgroundColor = [UIColor whiteColor];
        //self.font = [UIFont fontWithName:RobotoCondensedFont size:TextFieldFontSize];
        [self setFont:[UIFont fontWithName:RobotoRegularFont size:12.0]];

    }
}
- (CGRect)textRectForBounds:(CGRect)bounds {
    [self addBorderline ];
    return CGRectInset(bounds, 5, 5);
}

- (CGRect)editingRectForBounds:(CGRect)bounds {
    return CGRectInset(bounds, 5, 5);
}



@end
