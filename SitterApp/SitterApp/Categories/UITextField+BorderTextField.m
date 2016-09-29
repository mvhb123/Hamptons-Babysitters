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
     if ([[self superview] superview]&&!([[[self superview] superview] isMemberOfClass:NSClassFromString(@"_UIAlertControllerTextFieldView")] || [self  isMemberOfClass:NSClassFromString(@"UIAlertSheetTextField")] || [self  isMemberOfClass:NSClassFromString(@"_UIAlertControllerTextField")]))  {
        self.borderStyle = UITextBorderStyleNone;
        self.layer.borderWidth = 0.5;
        self.layer.borderColor = [UIColor grayColor].CGColor;
        self.backgroundColor = [UIColor whiteColor];
       // self.textColor = UIColorFromHexCode(0xCFD3D9);
        [self setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
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
