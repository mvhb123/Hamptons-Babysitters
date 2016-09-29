//
//  SMCustomRadioButton.m
//  DynamicallyExpand
//
//  Created by Shilpa Gade on 27/04/15.
//  Copyright (c) 2015 sofmen. All rights reserved.
//

#import "SMCustomRadioButton.h"
#define KDefaultFontColor [UIColor blackColor]
#define kDefaultFontType @"HelveticaNeue-regular"
#define kDefaultFontSize 25
#define kRadioButtonChk @"img_RadioChk"
#define kRadioButtonUnChk @"img_RadioUnChk"
#define kMale @"Male"
#define kFemale @"Female"
#define kButtonPosition @"true"
//true means all radio butons in a same line
//false means all radio buttons in a different line

@implementation SMCustomRadioButton


- (void)drawRect:(CGRect)rect
{
    [super drawRect:rect];
    maximumLabelSize = CGSizeMake(310, 9999);
    array_radio=[[NSMutableArray alloc] init];
    fontType=kDefaultFontType;
    fontSize=kDefaultFontSize;
    fontColor=KDefaultFontColor;
    str_FirstName=kMale;
    str_SecondName=kFemale;
    str_RadioClickedImg=kRadioButtonChk;
    str_RadioUnClickedImg=kRadioButtonUnChk;
}

/**
 Method is used to get the radio buttons text and images.
 @param NSString-firstText,secondText,clickRadioImgName,unclickedRadioImgName
 @return void
 */
- (void)setReqPropFroBtnState:(NSString *)firstText displaySecondText:(NSString *)secondText forCheckImageName:(NSString *)clickRadioImgName forUnchecked:(NSString *)unclickedRadioImgName buttonPosition:(BOOL)btn_Position{
    str_FirstName=firstText;
    str_SecondName=secondText;
    str_RadioClickedImg=clickRadioImgName;
    str_RadioUnClickedImg=unclickedRadioImgName;
    defaultsProOverwridden = YES;
    position=btn_Position;
    [self setFirstBtnFrame];
    [self setFirstBtnLblFrame];
    [self setSecondBtnFrame];
    [self setSecondBtnLblFrame];
}

/**
 Method is used to get the font type and font size for radio button lable.
 @param NSString-ftype,float-fSize,UIColor-tColor
 @return void
 */
-(void)setRadioBtnTextColorAndFont:(NSString *)ftype fontsize:(float)fSize forTextColor:(UIColor*) tColor
{
    fontType=ftype;
    fontSize=fSize;
    fontColor=tColor;
}

/**
 Method is used to set the first radio button frame.
 @param nill
 @return void
 */
-(void)setFirstBtnFrame
{
    float Center;
    btn_First = [UIButton buttonWithType:UIButtonTypeCustom];
    [btn_First setBackgroundImage:[UIImage imageNamed:str_RadioUnClickedImg] forState:UIControlStateNormal];
    [btn_First addTarget:self
                  action:@selector(firstRadioButtonPressed:)
        forControlEvents:UIControlEventTouchUpInside];
    [self addSubview:btn_First];
    if (position)
    {
        btn_First.frame=CGRectMake(5,self.frame.origin.y, self.frame.size.height-10, self.frame.size.height-10);
        Center = (self.frame.size.height -  btn_First.frame.size.height)/2;
    }
    else
    {
        btn_First.frame=CGRectMake(5,self.frame.origin.y, (self.frame.size.height/2)-5, (self.frame.size.height/2)-5);
        Center=(self.frame.size.height/2-btn_First.frame.size.height)/2;
        
    }
    CGRect buttonFrame = btn_First.frame;
    buttonFrame.origin.y = Center;
    btn_First.frame = buttonFrame;
}

/**
 Method is used to set the first radio button lable frame.
 @param nill
 @return void
 */
-(void)setFirstBtnLblFrame
{
    float width=(self.frame.size.width/2-(btn_First.frame.origin.x+btn_First.frame.size.width+2))-5;
    lbl_First=[[UILabel alloc] init];
    //UIFont *fontText = [UIFont fontWithName:fontType size:fontSize];
   /* CGRect textRect = [str_FirstName boundingRectWithSize:maximumLabelSize
                                             options:NSStringDrawingUsesLineFragmentOrigin
                                          attributes:@{NSFontAttributeName:fontText}
                                             context:nil];*/
    
  //  CGSize expectedLabelSize = CGSizeMake(textRect.size.width, textRect.size.height);
    [lbl_First setFrame:CGRectMake(btn_First.frame.origin.x+btn_First.frame.size.width+5,btn_First.frame.origin.y,width, btn_First.frame.size.height)];
    lbl_First.text=str_FirstName;
    lbl_First.numberOfLines=0;
   // lbl_First.adjustsFontSizeToFitWidth=YES;
    [self addSubview:lbl_First];
    lbl_First.textColor=fontColor;
    lbl_First.font=[UIFont fontWithName:fontType size:fontSize];
}

/**
 Method is used to set the second radio button frame.
 @param nill
 @return void
 */
-(void)setSecondBtnFrame
{
    btn_Second = [UIButton buttonWithType:UIButtonTypeCustom];
    [btn_Second setBackgroundImage:[UIImage imageNamed:kRadioButtonUnChk] forState:UIControlStateNormal];
    btn_Second.backgroundColor=[UIColor clearColor];
    [btn_Second addTarget:self
                   action:@selector(SecondRadioButtonPressed:)
         forControlEvents:UIControlEventTouchUpInside];
    [self addSubview:btn_Second];
    if (position)
    {
        btn_Second.frame=CGRectMake((self.frame.size.width/2), btn_First.frame.origin.y, self.frame.size.height-10,  self.frame.size.height-10);
        float Center = (self.frame.size.height -  btn_Second.frame.size.height)/2;
        CGRect buttonFrame = btn_Second.frame;
        buttonFrame.origin.y = Center;
        btn_Second.frame = buttonFrame;
    }
    else
    {
        btn_Second.frame=CGRectMake(btn_First.frame.origin.x,lbl_First.frame.origin.y+lbl_First.frame.size.height+5, btn_First.frame.size.width, btn_First.frame.size.height);
        self.frame=CGRectMake(self.frame.origin.x, self.frame.origin.y,self.frame.size.width,self.frame.size.height*2);
    }
}

/**
 Method is used to set the second radio button lable frame.
 @param nill
 @return void
 */
-(void)setSecondBtnLblFrame
{
    float width=(self.frame.size.width/2-(btn_First.frame.origin.x+btn_First.frame.size.width+2))-5;
    lbl_Second=[[UILabel alloc] init];
   // UIFont *fontText = [UIFont fontWithName:fontType size:fontSize];
  /* CGRect textRect = [str_SecondName boundingRectWithSize:maximumLabelSize
                                            options:NSStringDrawingUsesLineFragmentOrigin
                                         attributes:@{NSFontAttributeName:fontText}
                                            context:nil];*/
   //CGSize expectedLabelSize = CGSizeMake(textRect.size.width, textRect.size.height);
    
    [lbl_Second setFrame:CGRectMake(btn_Second.frame.origin.x+btn_Second.frame.size.width+5,btn_Second.frame.origin.y,width, btn_Second.frame.size.height)];
    lbl_Second.numberOfLines=0;
    lbl_Second.text=str_SecondName;
    //lbl_Second.adjustsFontSizeToFitWidth=YES;
    [self addSubview:lbl_Second];
    lbl_Second.textColor=fontColor;
    lbl_Second.font=[UIFont fontWithName:fontType size:fontSize];
}

/**
 On clicking this button user can be able to select the first radio button
 @param id A sender object which holds the UIButton object which initiated this action
 @return void
 */
- (void)firstRadioButtonPressed:(id)sender
{
     NSAssert(defaultsProOverwridden, @"Please overwride default values using setReqPropFroBtnState:(NSString *)firstText displaySecondText:(NSString *)secondText forCheckImageName:(NSString *)clickRadioImgName forUnchecked:(NSString *)unclickedRadioImgName: method");
    btnClicked=true;
    [self changeRadioBtnImg];
}

/**
 On clicking this button user can be able to select the second radio button
 @param id A sender object which holds the UIButton object which initiated this action
 @return void
 */
- (void)SecondRadioButtonPressed:(id)sender
{
     NSAssert(defaultsProOverwridden, @"Please overwride default values using setReqPropFroBtnState:(NSString *)firstText displaySecondText:(NSString *)secondText forCheckImageName:(NSString *)clickRadioImgName forUnchecked:(NSString *)unclickedRadioImgName: method");
     btnClicked=false;
    [self changeRadioBtnImg];
}

/**
 Method is used to change the images for radio buttons .
 @param nill
 @return void
 */
-(void)changeRadioBtnImg
{
    [btn_First setBackgroundImage:[UIImage imageNamed:str_RadioUnClickedImg] forState:UIControlStateNormal];
    [btn_Second setBackgroundImage:[UIImage imageNamed:str_RadioUnClickedImg] forState:UIControlStateNormal];
    if (btnClicked)
        [btn_First setBackgroundImage:[UIImage imageNamed:str_RadioClickedImg] forState:UIControlStateNormal];
    else
        [btn_Second setBackgroundImage:[UIImage imageNamed:str_RadioClickedImg] forState:UIControlStateNormal];
}

#pragma mark-MultipleButton
//in case of multiple buttons
-(void)setRadioBtn2Frame
{
    array_radio=[[NSMutableArray alloc] init];
    if (position)
    {
    float x=0;
    float width=self.frame.size.width/(arr_radioBtns.count);
    for (int i=0; i<arr_radioBtns.count; i++)
    {
        btn = [UIButton buttonWithType:UIButtonTypeCustom];
        [btn setBackgroundImage:[UIImage imageNamed:str_RadioUnClickedImg] forState:UIControlStateNormal];
        [btn addTarget:self
                      action:@selector(RadioButtonPressed:)
            forControlEvents:UIControlEventTouchUpInside];
        [self addSubview:btn];
        x=x+width;
        btn.frame=CGRectMake(x,self.frame.origin.y, self.frame.size.height-10, self.frame.size.height-10);
        float Center = (self.frame.size.height -  btn_Second.frame.size.height)/2;
        CGRect buttonFrame = btn.frame;
        buttonFrame.origin.y = Center;
        btn.frame = buttonFrame;
        [array_radio addObject:btn];
    }
}
    else
    {
        int y=0;
        float height=self.frame.size.height/arr_radioBtns.count;
        for (int i=0; i<arr_radioBtns.count; i++)
        {
            btn = [UIButton buttonWithType:UIButtonTypeCustom];
            [btn setBackgroundImage:[UIImage imageNamed:str_RadioUnClickedImg] forState:UIControlStateNormal];
            [btn addTarget:self
                    action:@selector(RadioButtonPressed:)
          forControlEvents:UIControlEventTouchUpInside];
            [self addSubview:btn];
            y=y+height;
            btn.frame=CGRectMake(5, y, (self.frame.size.height/arr_radioBtns.count)-5, (self.frame.size.height/arr_radioBtns.count)-5);
            [array_radio addObject:btn];
           
    }
    }
    [self setRadioLbl2Frame];
}

-(void)setRadioLbl2Frame
{
    if (!position)
    {
        for (int i=0; i<arr_radioBtns.count; i++)
        {
            UILabel *lbl=[[UILabel alloc] init];
            lbl.text=[arr_radioBtns objectAtIndex:i];
            lbl.textColor= fontColor;
            lbl.font=[UIFont fontWithName:fontType size:fontSize];
            UIButton *bt;//=[[UIButton alloc] init];
            bt=[array_radio objectAtIndex:i];
            CGRect buttonFrame=bt.frame;
            lbl.frame=CGRectMake(buttonFrame.origin.x+buttonFrame.size.width+5,buttonFrame.origin.y,self.frame.size.width-buttonFrame.size.width+2, buttonFrame.size.height);
            [self addSubview:lbl];
            
        }
    }
    
}
- (void)setBtnState:(NSArray *)arr_btn displaySecondText:(NSString *)secondText forCheckImageName:(NSString *)clickRadioImgName forUnchecked:(NSString *)unclickedRadioImgName buttonPosition:(BOOL)btn_Position{
    arr_radioBtns=arr_btn;
    str_RadioClickedImg=clickRadioImgName;
    str_RadioUnClickedImg=unclickedRadioImgName;
    defaultsProOverwridden = YES;
    position=btn_Position;
    [self setRadioBtn2Frame];
}

- (void)RadioButtonPressed:(id)sender
{
    [btn setBackgroundImage:[UIImage imageNamed:str_RadioUnClickedImg] forState:UIControlStateNormal];
    btn=(UIButton*)sender;
    [btn setBackgroundImage:[UIImage imageNamed:str_RadioClickedImg] forState:UIControlStateNormal];
    
    
}

@end
